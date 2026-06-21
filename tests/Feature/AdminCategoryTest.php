<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests (unauthenticated) cannot access category management.
     */
    public function test_unauthenticated_user_is_redirected_from_category_management(): void
    {
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->get('/admin/categories/create')->assertRedirect('/login');
        $this->post('/admin/categories', [])->assertRedirect('/login');
    }

    /**
     * Test that standard customers (non-admin) cannot access category management.
     */
    public function test_non_admin_user_gets_forbidden_status_from_category_management(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($user)->get('/admin/categories/create')->assertStatus(403);
        $this->actingAs($user)->post('/admin/categories', [])->assertStatus(403);
    }

    /**
     * Test that admins can access category pages.
     */
    public function test_admin_user_can_access_category_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $responseIndex = $this->actingAs($admin)->get('/admin/categories');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Category List');

        $responseCreate = $this->actingAs($admin)->get('/admin/categories/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Create Category');
    }

    /**
     * Test that admin can create root and child categories.
     */
    public function test_admin_can_create_categories_with_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $imageFile = UploadedFile::fake()->image('category-clothes.jpg');

        // 1. Create Root Category
        $responseRoot = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Clothing',
            'slug' => 'clothing-apparel',
            'description' => 'All sorts of premium clothing',
            'parent_id' => '',
            'is_active' => '1',
            'sort_order' => 10,
            'image' => $imageFile,
        ]);

        $responseRoot->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Clothing',
            'slug' => 'clothing-apparel',
            'parent_id' => null,
            'sort_order' => 10,
        ]);

        Storage::disk('public')->assertExists('categories/' . $imageFile->hashName());
        
        $rootCategory = Category::where('slug', 'clothing-apparel')->first();
        $this->assertStringContainsString('categories/' . $imageFile->hashName(), $rootCategory->image);

        // 2. Create Child Category
        $childImageFile = UploadedFile::fake()->image('child-category.jpg');
        $responseChild = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Shirts',
            'slug' => 'shirts',
            'description' => 'Shirts and tees',
            'parent_id' => $rootCategory->id,
            'is_active' => '1',
            'sort_order' => 5,
            'image' => $childImageFile,
        ]);

        $responseChild->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Shirts',
            'slug' => 'shirts',
            'parent_id' => $rootCategory->id,
            'sort_order' => 5,
        ]);
        Storage::disk('public')->assertExists('categories/' . $childImageFile->hashName());
    }

    /**
     * Test that admin can edit and update a category, replacing the image.
     */
    public function test_admin_can_update_category_and_replace_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $oldImageFile = UploadedFile::fake()->image('old-banner.jpg');
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Old description',
            'is_active' => true,
            'sort_order' => 1,
            'image' => Storage::disk('public')->url($oldImageFile->store('categories', 'public')),
        ]);

        Storage::disk('public')->assertExists('categories/' . $oldImageFile->hashName());

        $newImageFile = UploadedFile::fake()->image('new-banner.jpg');

        $response = $this->actingAs($admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Electronics Updated',
            'slug' => 'electronics-updated',
            'description' => 'New description',
            'parent_id' => '',
            'is_active' => '1',
            'sort_order' => 2,
            'image' => $newImageFile,
        ]);

        $response->assertRedirect('/admin/categories');
        
        $category->refresh();
        $this->assertEquals('Electronics Updated', $category->name);
        $this->assertEquals('electronics-updated', $category->slug);
        $this->assertEquals('New description', $category->description);
        $this->assertEquals(2, $category->sort_order);

        // Verify old image was deleted from disk
        Storage::disk('public')->assertMissing('categories/' . $oldImageFile->hashName());
        // Verify new image was saved to disk
        Storage::disk('public')->assertExists('categories/' . $newImageFile->hashName());
        $this->assertStringContainsString('categories/' . $newImageFile->hashName(), $category->image);
    }

    /**
     * Test category deletion restrictions.
     */
    public function test_admin_cannot_delete_category_with_products_or_subcategories(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create Parent Category
        $parentCategory = Category::create([
            'name' => 'Home',
            'slug' => 'home',
            'sort_order' => 1,
        ]);

        // Create Child Category
        $childCategory = Category::create([
            'name' => 'Kitchen',
            'slug' => 'kitchen',
            'parent_id' => $parentCategory->id,
            'sort_order' => 1,
        ]);

        // 1. Try to delete Parent Category (Should fail because of child category relationship)
        $responseDeleteParent = $this->actingAs($admin)->delete("/admin/categories/{$parentCategory->id}");
        $responseDeleteParent->assertRedirect('/admin/categories');
        $responseDeleteParent->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $parentCategory->id]);

        // Create Product and bind to Child Category
        $product = Product::create([
            'category_id' => $childCategory->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 25000,
            'sku' => 'TEST-SKU-1',
            'stock_quantity' => 10,
            'weight' => 500,
            'is_active' => true,
        ]);

        // 2. Try to delete Child Category (Should fail because of product relationship)
        $responseDeleteChild = $this->actingAs($admin)->delete("/admin/categories/{$childCategory->id}");
        $responseDeleteChild->assertRedirect('/admin/categories');
        $responseDeleteChild->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $childCategory->id]);

        // 3. Remove product and subcategory
        $product->delete();
        $childCategory->delete();

        // 4. Try to delete Parent Category now (Should succeed)
        $responseDeleteParentSuccess = $this->actingAs($admin)->delete("/admin/categories/{$parentCategory->id}");
        $responseDeleteParentSuccess->assertRedirect('/admin/categories');
        $responseDeleteParentSuccess->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $parentCategory->id]);
    }
}

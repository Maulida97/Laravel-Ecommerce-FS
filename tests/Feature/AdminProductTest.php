<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests (unauthenticated) cannot access product management.
     */
    public function test_unauthenticated_user_is_redirected_from_product_management(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/admin/products/create')->assertRedirect('/login');
        $this->post('/admin/products', [])->assertRedirect('/login');
    }

    /**
     * Test that standard customers (non-admin) cannot access product management.
     */
    public function test_non_admin_user_gets_forbidden_status_from_product_management(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user)->get('/admin/products')->assertStatus(403);
        $this->actingAs($user)->get('/admin/products/create')->assertStatus(403);
        $this->actingAs($user)->post('/admin/products', [])->assertStatus(403);
    }

    /**
     * Test that admins can access product index, create and edit pages.
     */
    public function test_admin_user_can_access_product_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Running Shoes',
            'slug' => 'running-shoes',
            'price' => 500000,
            'sku' => 'SHOES-01',
            'stock_quantity' => 10,
            'weight' => 600,
        ]);

        $responseIndex = $this->actingAs($admin)->get('/admin/products');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Product List');
        $responseIndex->assertSee('Running Shoes');

        $responseCreate = $this->actingAs($admin)->get('/admin/products/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Create Product');

        $responseEdit = $this->actingAs($admin)->get("/admin/products/{$product->id}/edit");
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Edit Product');
    }

    /**
     * Test creating a basic product (without variants) with multiple images.
     */
    public function test_admin_can_create_basic_product_with_multiple_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $img1 = UploadedFile::fake()->image('shirt-front.jpg');
        $img2 = UploadedFile::fake()->image('shirt-back.jpg');

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Cotton Shirt',
            'category_id' => $category->id,
            'description' => 'Super comfortable cotton shirt',
            'short_description' => 'Cotton shirt',
            'price' => 125000,
            'compare_at_price' => 150000,
            'sku' => 'SHIRT-COT-01',
            'stock_quantity' => 50,
            'weight' => 250,
            'is_active' => '1',
            'is_featured' => '1',
            'primary_image_index' => 0,
            'images' => [$img1, $img2],
        ]);

        $response->assertRedirect('/admin/products');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Cotton Shirt',
            'sku' => 'SHIRT-COT-01',
            'stock_quantity' => 50,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $product = Product::where('sku', 'SHIRT-COT-01')->first();
        
        // Assert images are created in DB
        $this->assertCount(2, $product->images);
        $this->assertTrue($product->images[0]->is_primary);
        $this->assertFalse($product->images[1]->is_primary);

        // Assert files exist on storage disk
        Storage::disk('public')->assertExists('products/' . $img1->hashName());
        Storage::disk('public')->assertExists('products/' . $img2->hashName());
    }

    /**
     * Test creating a product with variations.
     */
    public function test_admin_can_create_product_with_variations(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Form sends variants as a JSON payload
        $variantsJson = json_encode([
            [
                'name' => 'Red - S',
                'sku' => 'VAR-RED-S',
                'price_adjustment' => 10000.00,
                'stock_quantity' => 15,
                'is_active' => true,
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Red', 'color_code' => '#ff0000'],
                    ['name' => 'Size', 'value' => 'S']
                ]
            ],
            [
                'name' => 'Blue - M',
                'sku' => 'VAR-BLUE-M',
                'price_adjustment' => 15000.00,
                'stock_quantity' => 25,
                'is_active' => true,
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Blue', 'color_code' => '#0000ff'],
                    ['name' => 'Size', 'value' => 'M']
                ]
            ]
        ]);

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Designer Jeans',
            'category_id' => $category->id,
            'price' => 300000,
            'sku' => 'JEANS-DSG-01',
            'weight' => 500,
            'is_active' => '1',
            'has_variants' => '1',
            'variants_json' => $variantsJson,
        ]);

        $response->assertRedirect('/admin/products');
        
        $product = Product::where('sku', 'JEANS-DSG-01')->first();
        
        // Assert variations sum into total stock: 15 + 25 = 40
        $this->assertEquals(40, $product->stock_quantity);

        // Assert variants created in database
        $this->assertCount(2, $product->variants);
        
        $var1 = ProductVariant::where('sku', 'VAR-RED-S')->first();
        $this->assertEquals('Red - S', $var1->variant_name);
        $this->assertEquals(10000.00, $var1->price_adjustment);
        $this->assertEquals(15, $var1->stock_quantity);

        // Assert attribute and combination mapping
        $this->assertCount(2, $var1->combinations);
        $this->assertEquals('Color', $var1->combinations[0]->attribute->name);
        $this->assertEquals('Red', $var1->combinations[0]->value);
    }

    /**
     * Test updating product details, variants and replacing images.
     */
    public function test_admin_can_update_product_images_and_variants(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Create product with existing image
        $oldFile = UploadedFile::fake()->image('old-pic.jpg');
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Legacy Watch',
            'slug' => 'legacy-watch',
            'price' => 1000000,
            'sku' => 'WATCH-01',
            'stock_quantity' => 5,
            'weight' => 150,
        ]);
        
        $oldImg = ProductImage::create([
            'product_id' => $product->id,
            'image_url' => Storage::disk('public')->url($oldFile->store('products', 'public')),
            'public_id' => 'local_products_' . $oldFile->hashName(),
            'is_primary' => true,
        ]);

        Storage::disk('public')->assertExists('products/' . $oldFile->hashName());

        $newFile = UploadedFile::fake()->image('new-pic.jpg');
        
        // New variations JSON
        $newVariantsJson = json_encode([
            [
                'name' => 'Gold',
                'sku' => 'VAR-WATCH-GOLD',
                'price_adjustment' => 200000.00,
                'stock_quantity' => 2,
                'is_active' => true,
                'attributes' => [
                    ['name' => 'Material', 'value' => 'Gold']
                ]
            ]
        ]);

        $response = $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => 'Legacy Watch Gold Edition',
            'category_id' => $category->id,
            'price' => 1200000,
            'sku' => 'WATCH-01-GOLD',
            'weight' => 180,
            'is_active' => '1',
            'has_variants' => '1',
            'variants_json' => $newVariantsJson,
            'deleted_images' => $oldImg->id, // Mark old image as deleted
            'images' => [$newFile],
            'primary_image_index' => 0, // Set new image as primary
        ]);

        $response->assertRedirect('/admin/products');

        $product->refresh();
        $this->assertEquals('Legacy Watch Gold Edition', $product->name);
        $this->assertEquals('WATCH-01-GOLD', $product->sku);
        
        // Old image deleted from DB and Disk
        $this->assertDatabaseMissing('product_images', ['id' => $oldImg->id]);
        Storage::disk('public')->assertMissing('products/' . $oldFile->hashName());

        // New image stored in DB and Disk
        $this->assertCount(1, $product->images);
        $this->assertTrue($product->images[0]->is_primary);
        Storage::disk('public')->assertExists('products/' . $newFile->hashName());

        // Variations are updated
        $this->assertCount(1, $product->variants);
        $this->assertEquals('VAR-WATCH-GOLD', $product->variants[0]->sku);
        $this->assertEquals(2, $product->stock_quantity); // Stock synced
    }

    /**
     * Test single deletion cleans up database and storage fakes.
     */
    public function test_admin_can_delete_product(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $file = UploadedFile::fake()->image('bag.jpg');
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Leather Bag',
            'slug' => 'leather-bag',
            'price' => 450000,
            'sku' => 'BAG-01',
            'stock_quantity' => 10,
            'weight' => 800,
        ]);
        
        $img = ProductImage::create([
            'product_id' => $product->id,
            'image_url' => Storage::disk('public')->url($file->store('products', 'public')),
            'public_id' => 'local_products_' . $file->hashName(),
            'is_primary' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Large',
            'sku' => 'BAG-01-LG',
            'stock_quantity' => 10,
        ]);

        Storage::disk('public')->assertExists('products/' . $file->hashName());

        $response = $this->actingAs($admin)->delete("/admin/products/{$product->id}");
        $response->assertRedirect('/admin/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['id' => $img->id]);
        $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);

        Storage::disk('public')->assertMissing('products/' . $file->hashName());
    }

    /**
     * Test bulk delete operations.
     */
    public function test_admin_can_bulk_delete_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $p1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Product 1',
            'slug' => 'product-1',
            'price' => 10000,
            'sku' => 'SKU-P1',
            'weight' => 100,
        ]);

        $p2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Product 2',
            'slug' => 'product-2',
            'price' => 20000,
            'sku' => 'SKU-P2',
            'weight' => 200,
        ]);

        $response = $this->actingAs($admin)->post('/admin/products/bulk', [
            'ids' => [$p1->id, $p2->id],
            'action' => 'delete',
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseMissing('products', ['id' => $p1->id]);
        $this->assertDatabaseMissing('products', ['id' => $p2->id]);
    }

    /**
     * Test bulk status activations/deactivations.
     */
    public function test_admin_can_bulk_update_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $p1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Product 1',
            'slug' => 'product-1',
            'price' => 10000,
            'sku' => 'SKU-P1',
            'weight' => 100,
            'is_active' => false,
        ]);

        $p2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Product 2',
            'slug' => 'product-2',
            'price' => 20000,
            'sku' => 'SKU-P2',
            'weight' => 200,
            'is_active' => false,
        ]);

        // Bulk Activate
        $responseActive = $this->actingAs($admin)->post('/admin/products/bulk', [
            'ids' => [$p1->id, $p2->id],
            'action' => 'activate',
        ]);

        $responseActive->assertRedirect('/admin/products');
        $this->assertTrue($p1->fresh()->is_active);
        $this->assertTrue($p2->fresh()->is_active);

        // Bulk Deactivate
        $responseInactive = $this->actingAs($admin)->post('/admin/products/bulk', [
            'ids' => [$p1->id, $p2->id],
            'action' => 'deactivate',
        ]);

        $responseInactive->assertRedirect('/admin/products');
        $this->assertFalse($p1->fresh()->is_active);
        $this->assertFalse($p2->fresh()->is_active);
    }
}

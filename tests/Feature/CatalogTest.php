<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use App\Models\User;
use App\Livewire\Catalog;
use App\Livewire\NavbarSearch;
use App\Livewire\NewsletterForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $product1;
    private $product2;
    private $slide;

    protected function setUp(): void
    {
        parent::setUp();

        // Create initial seed data for test cases
        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Gadgets and gear',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->product1 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Wireless Headphones Premium',
            'slug' => 'wireless-headphones-premium',
            'description' => 'Experience ultimate sound quality with active noise cancelation.',
            'price' => 250000,
            'compare_at_price' => 300000,
            'sku' => 'EL-HP-001',
            'stock_quantity' => 10,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $this->product2 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Classic Leather Bag',
            'slug' => 'classic-leather-bag',
            'description' => 'Handcrafted brown leather messenger bag.',
            'price' => 750000,
            'sku' => 'AC-BG-001',
            'stock_quantity' => 5,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $this->slide = Slide::create([
            'title' => 'Big Season Sale',
            'subtitle' => 'Get up to 50% discount on bags.',
            'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=600&fit=crop',
            'button_text' => 'Shop Now',
            'button_link' => '#products',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Test storefront home page works and renders dynamic data.
     */
    public function test_storefront_homepage_renders_dynamic_data(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee($this->slide->title);
        $response->assertSee($this->category->name);
        $response->assertSee($this->product1->name);
        // Product 2 is not featured, so it shouldn't show in the featured section
        $response->assertDontSee($this->product2->name);
    }

    /**
     * Test catalog route renders properly.
     */
    public function test_catalog_page_renders_successfully(): void
    {
        $response = $this->get('/products');
        
        $response->assertStatus(200);
        $response->assertSee($this->product1->name);
        $response->assertSee($this->product2->name);
    }

    /**
     * Test catalog search filter functionality.
     */
    public function test_catalog_search_filters_products(): void
    {
        Livewire::test(Catalog::class)
            ->set('search', 'Headphones')
            ->assertSee($this->product1->name)
            ->assertDontSee($this->product2->name);
    }

    /**
     * Test catalog category filter functionality.
     */
    public function test_catalog_category_filters_products(): void
    {
        // Setup another category and product
        $anotherCategory = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $fashionProduct = Product::create([
            'category_id' => $anotherCategory->id,
            'name' => 'Cotton T-Shirt',
            'slug' => 'cotton-t-shirt',
            'price' => 150000,
            'sku' => 'FS-TS-001',
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        Livewire::test(Catalog::class)
            ->set('selectedCategories', ['fashion'])
            ->assertSee($fashionProduct->name)
            ->assertDontSee($this->product1->name);
    }

    /**
     * Test catalog price range filtering.
     */
    public function test_catalog_price_range_filters_products(): void
    {
        Livewire::test(Catalog::class)
            ->set('minPrice', 100000)
            ->set('maxPrice', 300000)
            ->assertSee($this->product1->name)
            ->assertDontSee($this->product2->name);
    }

    /**
     * Test catalog sorting functionality.
     */
    public function test_catalog_sorting_orders_products_correctly(): void
    {
        // Low to High sort: Headphones (250K) then Leather Bag (750K)
        Livewire::test(Catalog::class)
            ->set('sortBy', 'price_asc')
            ->assertSeeInOrder([$this->product1->name, $this->product2->name]);

        // High to Low sort: Leather Bag (750K) then Headphones (250K)
        Livewire::test(Catalog::class)
            ->set('sortBy', 'price_desc')
            ->assertSeeInOrder([$this->product2->name, $this->product1->name]);
    }

    /**
     * Test navbar search suggestions.
     */
    public function test_navbar_search_displays_suggestions(): void
    {
        Livewire::test(NavbarSearch::class)
            ->set('query', 'Headphones')
            ->assertSee($this->product1->name)
            ->assertDontSee($this->product2->name);
    }

    /**
     * Test newsletter subscription works.
     */
    public function test_newsletter_subscribes_successfully(): void
    {
        Livewire::test(NewsletterForm::class)
            ->set('email', 'subscriber@example.com')
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSee('Thank you for subscribing');

        $this->assertDatabaseHas('newsletters', [
            'email' => 'subscriber@example.com',
        ]);
    }

    /**
     * Test newsletter validations fail on invalid emails.
     */
    public function test_newsletter_fails_on_invalid_email(): void
    {
        Livewire::test(NewsletterForm::class)
            ->set('email', 'not-an-email')
            ->call('subscribe')
            ->assertHasErrors(['email' => 'email']);
            
        $this->assertDatabaseMissing('newsletters', [
            'email' => 'not-an-email',
        ]);
    }

    /**
     * Test addToCart on Catalog component adds item to cart successfully.
     */
    public function test_catalog_add_to_cart_directly(): void
    {
        // Assert cart is empty
        $cartService = app(\App\Services\CartService::class);
        $this->assertEquals(0, $cartService->getItemCount());

        // Call addToCart on the Catalog component
        Livewire::test(Catalog::class)
            ->call('addToCart', $this->product1->id)
            ->assertDispatched('cart-updated');

        // Assert cart contains the item
        $this->assertEquals(1, $cartService->getItemCount());
    }
}

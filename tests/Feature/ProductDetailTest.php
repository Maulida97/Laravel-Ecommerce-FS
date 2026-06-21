<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use App\Models\User;
use App\Livewire\ProductDetail;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $productWithVariants;
    private $productNoVariants;
    private $colorAttr;
    private $sizeAttr;
    private $redColor;
    private $mSize;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->colorAttr = VariantAttribute::create(['name' => 'Color']);
        $this->sizeAttr = VariantAttribute::create(['name' => 'Size']);

        $this->redColor = VariantAttributeValue::create([
            'variant_attribute_id' => $this->colorAttr->id,
            'value' => 'Red',
            'color_code' => '#FF0000',
        ]);

        $this->mSize = VariantAttributeValue::create([
            'variant_attribute_id' => $this->sizeAttr->id,
            'value' => 'M',
        ]);

        // Product with variants
        $this->productWithVariants = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Premium Shirt',
            'slug' => 'premium-shirt',
            'description' => 'A beautiful organic cotton shirt.',
            'short_description' => 'Organic cotton shirt.',
            'price' => 100000,
            'sku' => 'FS-SH-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $this->productWithVariants->id,
            'variant_name' => 'Red - M',
            'sku' => 'FS-SH-001-RED-M',
            'price_adjustment' => 15000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $variant->combinations()->attach([$this->redColor->id, $this->mSize->id]);

        // Product without variants
        $this->productNoVariants = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Classic Watch',
            'slug' => 'classic-watch',
            'description' => 'A vintage leather watch.',
            'price' => 500000,
            'sku' => 'AC-WT-001',
            'stock_quantity' => 8,
            'is_active' => true,
        ]);
    }

    /**
     * Test product detail page renders successfully.
     */
    public function test_product_detail_page_renders_successfully(): void
    {
        $response = $this->get('/products/' . $this->productNoVariants->slug);
        
        $response->assertStatus(200);
        $response->assertSee($this->productNoVariants->name);
        $response->assertSee('SKU:');
        $response->assertSee($this->productNoVariants->sku);
    }

    /**
     * Test variant attribute selection updates stock and price.
     */
    public function test_variant_selection_updates_price_and_stock(): void
    {
        // Initial load of product with variants pre-selects the first available variant (Red - M)
        Livewire::test(ProductDetail::class, ['product' => $this->productWithVariants])
            ->assertSet('selectedAttributes', [
                $this->colorAttr->id => $this->redColor->id,
                $this->sizeAttr->id => $this->mSize->id,
            ])
            ->assertSee('SKU:')
            ->assertSee('FS-SH-001-RED-M')
            // Base price 100k + 15k adjustment = 115k
            ->assertSee('Rp 115.000')
            ->assertSee('Only 5 left in stock');
    }

    /**
     * Test quantity selector constraints.
     */
    public function test_quantity_increment_decrement(): void
    {
        Livewire::test(ProductDetail::class, ['product' => $this->productNoVariants])
            ->assertSet('quantity', 1)
            ->call('increment')
            ->assertSet('quantity', 2)
            ->call('decrement')
            ->assertSet('quantity', 1)
            // Cannot decrement below 1
            ->call('decrement')
            ->assertSet('quantity', 1);

        // Cannot increment above stock (stock is 8)
        $comp = Livewire::test(ProductDetail::class, ['product' => $this->productNoVariants]);
        for ($i = 0; $i < 12; $i++) {
            $comp->call('increment');
        }
        $comp->assertSet('quantity', 8);
    }

    /**
     * Test adding to cart creates database cart item.
     */
    public function test_add_to_cart_creates_cart_item(): void
    {
        // For guest user
        Livewire::test(ProductDetail::class, ['product' => $this->productNoVariants])
            ->set('quantity', 2)
            ->call('addToCart')
            ->assertHasNoErrors()
            ->assertDispatched('cart-updated');

        // Check database
        $cart = Cart::where('session_id', session()->getId())->first();
        $this->assertNotNull($cart);
        $this->assertEquals(1, $cart->items()->count());

        $item = $cart->items()->first();
        $this->assertEquals($this->productNoVariants->id, $item->product_id);
        $this->assertNull($item->product_variant_id);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(500000, $item->unit_price);
    }

    /**
     * Test adding to cart as authenticated user.
     */
    public function test_add_to_cart_as_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ProductDetail::class, ['product' => $this->productWithVariants])
            ->call('addToCart')
            ->assertHasNoErrors()
            ->assertDispatched('cart-updated');

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        
        $item = $cart->items()->first();
        $this->assertEquals($this->productWithVariants->id, $item->product_id);
        $this->assertNotNull($item->product_variant_id); // Variant ID of Red-M
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\User;
use App\Livewire\CartPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Premium Shirt',
            'slug' => 'premium-shirt',
            'description' => 'A shirt.',
            'price' => 100000,
            'sku' => 'FS-SH-001',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }

    public function test_cart_page_renders_empty_state_correctly(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSee('Your cart is empty');
    }

    public function test_cart_operations(): void
    {
        // 1. Add item to cart
        $cart = Cart::create(['session_id' => session()->getId()]);
        $item = $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        // 2. View cart page
        Livewire::test(CartPage::class)
            ->assertSee('Premium Shirt')
            ->assertSee('Rp 200.000')
            // 3. Test increment
            ->call('incrementItem', $item->id)
            ->assertDispatched('cart-updated');

        $this->assertEquals(3, $item->fresh()->quantity);

        // 4. Test decrement
        Livewire::test(CartPage::class)
            ->call('decrementItem', $item->id)
            ->assertDispatched('cart-updated');

        $this->assertEquals(2, $item->fresh()->quantity);

        // 5. Test remove
        Livewire::test(CartPage::class)
            ->call('removeItem', $item->id)
            ->assertDispatched('cart-updated');

        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_clear_cart(): void
    {
        $cart = Cart::create(['session_id' => session()->getId()]);
        $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        Livewire::test(CartPage::class)
            ->call('clearCart')
            ->assertDispatched('cart-updated');

        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_guest_cart_merges_on_login(): void
    {
        // Add item as guest
        $sessionId = session()->getId();
        $guestCart = Cart::create(['session_id' => $sessionId]);
        $guestCart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        $user = User::factory()->create();
        
        // Call mergeGuestCart
        app(\App\Services\CartService::class)->mergeGuestCart($user->id);

        $this->assertDatabaseMissing('carts', ['session_id' => $sessionId]);
        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);
        $this->assertEquals(1, $userCart->items()->count());
        $this->assertEquals(2, $userCart->items()->first()->quantity);
    }
}

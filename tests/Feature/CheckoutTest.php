<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Livewire\CheckoutPage;
use App\Livewire\OrderTrackPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
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

    public function test_checkout_redirects_to_cart_if_empty(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/cart');
    }

    public function test_checkout_renders_successfully_with_items(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Shipping Information');
        $response->assertSee('Premium Shirt');
    }

    public function test_checkout_form_validation(): void
    {
        $cart = Cart::create(['session_id' => session()->getId()]);
        $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        Livewire::test(CheckoutPage::class)
            ->call('placeOrder')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'city' => 'required',
                'postal_code' => 'required',
            ]);
    }

    public function test_checkout_place_order_generates_snap_token(): void
    {
        $cart = Cart::create(['session_id' => session()->getId()]);
        $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        Livewire::test(CheckoutPage::class)
            ->set('name', 'Andi Wijaya')
            ->set('email', 'andi@example.com')
            ->set('phone', '0812345678')
            ->set('address', 'Jl. Merdeka No. 10')
            ->set('city', 'Jakarta')
            ->set('postal_code', '12345')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertDispatched('initiate-payment')
            ->assertDispatched('cart-updated');

        $this->assertEquals(0, $cart->items()->count());
        $this->assertEquals(1, Order::count());
        
        $order = Order::first();
        $this->assertEquals('Andi Wijaya', $order->guest_name);
        $this->assertEquals(200000, $order->subtotal);
        $this->assertEquals(15000, $order->shipping_cost); // subtotal < 500k gets 15k shipping
        $this->assertEquals(215000, $order->total_amount);
    }

    public function test_midtrans_webhook_handles_successful_payment(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-123',
            'shipping_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'billing_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'subtotal' => 100000,
            'total_amount' => 115000,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'guest_email' => 'andi@example.com',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        $payload = [
            'order_id' => 'ORD-TEST-123',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-tx-001',
            'gross_amount' => '115000.00',
        ];

        // Post request to webhook (bypassing CSRF)
        $response = $this->postJson('/webhook/midtrans', $payload);
        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);
    }

    public function test_checkout_success_page_renders_successfully(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-456',
            'shipping_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'billing_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'subtotal' => 100000,
            'total_amount' => 115000,
            'payment_status' => 'paid',
            'order_status' => 'processing',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        $response = $this->get('/checkout/success?order_id=ORD-TEST-456');
        $response->assertStatus(200);
        $response->assertSee('Order Placed Successfully!');
        $response->assertSee('ORD-TEST-456');
    }

    public function test_order_tracking_page(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-789',
            'shipping_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'billing_address' => ['name' => 'Andi', 'email' => 'andi@example.com', 'phone' => '081', 'address' => 'addr', 'city' => 'city', 'postal_code' => '123'],
            'subtotal' => 100000,
            'total_amount' => 115000,
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'guest_email' => 'andi@example.com',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        // Access page without parameters
        $response = $this->get('/order-track');
        $response->assertStatus(200);

        // Search order via Livewire
        Livewire::test(OrderTrackPage::class)
            ->set('order_number', 'ORD-TEST-789')
            ->set('email', 'andi@example.com')
            ->call('trackOrder')
            ->assertHasNoErrors()
            ->assertSeeHtml('Paid')
            ->assertSeeHtml('Processing')
            ->assertSee('Rp 115.000');
    }
}

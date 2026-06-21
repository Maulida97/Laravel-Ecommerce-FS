<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        // Create a category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Create a product for order items
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-SKU-123',
            'price' => 100000,
            'stock_quantity' => 10,
            'weight' => 100,
        ]);

        // Create a base order
        $this->order = Order::create([
            'order_number' => 'ORD-TEST-0001',
            'user_id' => $this->customer->id,
            'shipping_address' => [
                'recipient_name' => 'Receiver Name',
                'phone' => '081234567890',
                'address_line' => 'Main Street No. 5',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12345',
            ],
            'subtotal' => 100000,
            'shipping_cost' => 15000,
            'total_amount' => 115000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        // Create initial status log
        OrderStatusHistory::create([
            'order_id' => $this->order->id,
            'status' => 'pending',
            'notes' => 'Order created.',
        ]);
    }

    /**
     * Test guest cannot access any order admin routes.
     */
    public function test_guest_is_blocked_from_order_admin_routes(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect('/login');
        $this->get(route('admin.orders.show', $this->order->id))->assertRedirect('/login');
        $this->put(route('admin.orders.update', $this->order->id), ['order_status' => 'processing'])->assertRedirect('/login');
        $this->post(route('admin.orders.tracking', $this->order->id), ['tracking_number' => '1234'])->assertRedirect('/login');
        $this->get(route('admin.orders.invoice', $this->order->id))->assertRedirect('/login');
    }

    /**
     * Test normal customer is blocked from order admin routes.
     */
    public function test_customer_is_forbidden_from_order_admin_routes(): void
    {
        $this->actingAs($this->customer);

        $this->get(route('admin.orders.index'))->assertStatus(403);
        $this->get(route('admin.orders.show', $this->order->id))->assertStatus(403);
        $this->put(route('admin.orders.update', $this->order->id), ['order_status' => 'processing'])->assertStatus(403);
        $this->post(route('admin.orders.tracking', $this->order->id), ['tracking_number' => '1234'])->assertStatus(403);
        $this->get(route('admin.orders.invoice', $this->order->id))->assertStatus(403);
    }

    /**
     * Test admin can access order index list.
     */
    public function test_admin_can_access_orders_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Manage Orders');
        $response->assertSee($this->order->order_number);
    }

    /**
     * Test search filters.
     */
    public function test_admin_can_search_orders(): void
    {
        // Add another order
        Order::create([
            'order_number' => 'ORD-OTHER-9999',
            'guest_name' => 'Guest Buyer',
            'guest_email' => 'guest@example.com',
            'shipping_address' => ['recipient_name' => 'Guest'],
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $this->actingAs($this->admin);

        // Search match
        $responseMatch = $this->get(route('admin.orders.index', ['search' => 'ORD-TEST']));
        $responseMatch->assertSee($this->order->order_number);
        $responseMatch->assertDontSee('ORD-OTHER-9999');

        // Search guest name
        $responseGuest = $this->get(route('admin.orders.index', ['search' => 'Guest Buyer']));
        $responseGuest->assertSee('ORD-OTHER-9999');
        $responseGuest->assertDontSee($this->order->order_number);
    }

    /**
     * Test status filtering.
     */
    public function test_admin_can_filter_orders_by_status(): void
    {
        // Update order status of base order to processing
        $this->order->update(['order_status' => 'processing', 'payment_status' => 'paid']);

        // Create a pending order
        $pendingOrder = Order::create([
            'order_number' => 'ORD-PENDING-999',
            'user_id' => $this->customer->id,
            'shipping_address' => ['recipient_name' => 'Test'],
            'subtotal' => 20000,
            'total_amount' => 20000,
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $this->actingAs($this->admin);

        // Filter order_status = processing
        $response = $this->get(route('admin.orders.index', ['order_status' => 'processing']));
        $response->assertSee($this->order->order_number);
        $response->assertDontSee($pendingOrder->order_number);

        // Filter payment_status = pending
        $responsePay = $this->get(route('admin.orders.index', ['payment_status' => 'pending']));
        $responsePay->assertSee($pendingOrder->order_number);
        $responsePay->assertDontSee($this->order->order_number);
    }

    /**
     * Test admin can view order show page.
     */
    public function test_admin_can_view_order_details(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertSee($this->order->order_number);
        $response->assertSee('Test Product');
        $response->assertSee('Receiver Name');
    }

    /**
     * Test updating order status to processing (no tracking number required).
     */
    public function test_admin_can_update_status_to_processing(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.orders.update', $this->order->id), [
            'order_status' => 'processing',
            'notes' => 'Pesanan mulai diproses.',
        ]);

        $response->assertRedirect(route('admin.orders.show', $this->order->id));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals('processing', $this->order->order_status);

        // Verify status histories logged correctly
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $this->order->id,
            'status' => 'processing',
            'notes' => 'Pesanan mulai diproses.',
            'changed_by' => $this->admin->id,
        ]);
    }

    /**
     * Test updating status to shipped requires a tracking number.
     */
    public function test_updating_status_to_shipped_requires_tracking_number(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('admin.orders.show', $this->order->id))
            ->put(route('admin.orders.update', $this->order->id), [
                'order_status' => 'shipped',
                'tracking_number' => '', // Empty
                'notes' => 'Sending items',
            ]);

        $response->assertRedirect(route('admin.orders.show', $this->order->id));
        $response->assertSessionHasErrors('tracking_number');

        $this->order->refresh();
        $this->assertNotEquals('shipped', $this->order->order_status);
    }

    /**
     * Test updating status to shipped with tracking number works.
     */
    public function test_admin_can_update_status_to_shipped_with_tracking_number(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.orders.update', $this->order->id), [
            'order_status' => 'shipped',
            'tracking_number' => 'RESI123456789',
            'notes' => 'Resi pengiriman dibuat.',
        ]);

        $response->assertRedirect(route('admin.orders.show', $this->order->id));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals('shipped', $this->order->order_status);
        $this->assertEquals('RESI123456789', $this->order->tracking_number);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $this->order->id,
            'status' => 'shipped',
            'notes' => 'Resi pengiriman dibuat.',
            'changed_by' => $this->admin->id,
        ]);
    }

    /**
     * Test updating tracking number separately.
     */
    public function test_admin_can_update_tracking_number_separately(): void
    {
        $this->actingAs($this->admin);

        // Put order status to shipped first
        $this->order->update(['order_status' => 'shipped', 'tracking_number' => 'OLDRESI']);

        $response = $this->post(route('admin.orders.tracking', $this->order->id), [
            'tracking_number' => 'NEWRESI-999',
            'notes' => 'Koreksi resi.',
        ]);

        $response->assertRedirect(route('admin.orders.show', $this->order->id));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals('NEWRESI-999', $this->order->tracking_number);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $this->order->id,
            'status' => 'shipped',
            'notes' => 'Koreksi resi.',
            'changed_by' => $this->admin->id,
        ]);
    }

    /**
     * Test admin can access printable invoice page.
     */
    public function test_admin_can_view_invoice(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.invoice', $this->order->id));

        $response->assertStatus(200);
        $response->assertSee('Invoice');
        $response->assertSee($this->order->order_number);
        $response->assertSee('Receiver Name');
    }
}

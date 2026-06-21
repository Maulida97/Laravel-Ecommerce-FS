<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get references
        $customer = User::where('role', 'customer')->first();
        $admin = User::where('role', 'admin')->first();

        $headphones = Product::where('sku', 'EL-HP-WNCX1')->first();
        $watch = Product::where('sku', 'EL-SW-S5')->first();
        $sweatshirt = Product::where('sku', 'FA-SS-OCC')->first();
        $bag = Product::where('sku', 'AC-BG-VLM')->first();

        $addressAndi = [
            'recipient_name' => 'Andi Wijaya',
            'phone' => '+628777665544',
            'address_line' => 'Jl. Kebagusan Raya No. 45',
            'city' => 'Jakarta Selatan',
            'state' => 'DKI Jakarta',
            'postal_code' => '12520',
        ];

        $addressBudi = [
            'recipient_name' => 'Budi Santoso',
            'phone' => '+6281299993333',
            'address_line' => 'Ruko Sentra Bisnis Blok C No. 12, Jl. Pemuda',
            'city' => 'Surabaya',
            'state' => 'Jawa Timur',
            'postal_code' => '60173',
        ];

        $addressSiti = [
            'recipient_name' => 'Siti Aminah',
            'phone' => '+6285640001111',
            'address_line' => 'Perumahan Griya Indah D-7, Ngaliyan',
            'city' => 'Semarang',
            'state' => 'Jawa Tengah',
            'postal_code' => '50181',
        ];

        // --- Order 1: Paid, Status: Processing (Customer: Andi) ---
        $order1 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0001',
            'user_id' => $customer->id,
            'shipping_address' => $addressAndi,
            'subtotal' => 3499000,
            'shipping_cost' => 15000,
            'total_amount' => 3514000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'paid',
            'midtrans_transaction_id' => 'midtrans-tx-1111',
            'midtrans_payment_type' => 'qris',
            'midtrans_transaction_status' => 'settlement',
            'order_status' => 'processing',
            'notes' => 'Tolong dibungkus kado.',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $headphones->id,
            'product_name' => $headphones->name,
            'sku' => $headphones->sku,
            'quantity' => 1,
            'unit_price' => 3499000,
            'total_price' => 3499000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order1->id,
            'status' => 'pending',
            'notes' => 'Pesanan berhasil dibuat.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order1->id,
            'status' => 'processing',
            'notes' => 'Pembayaran lunas terverifikasi via Midtrans. Status berubah menjadi Processing.',
        ]);


        // --- Order 2: Paid, Status: Shipped (Guest: Budi) ---
        $order2 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0002',
            'user_id' => null,
            'guest_name' => 'Budi Santoso',
            'guest_email' => 'budi@gmail.com',
            'guest_phone' => '+6281299993333',
            'shipping_address' => $addressBudi,
            'subtotal' => 4190000, // Watch (2990000) + Bag (1200000)
            'shipping_cost' => 20000,
            'total_amount' => 4210000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'paid',
            'midtrans_transaction_id' => 'midtrans-tx-2222',
            'midtrans_payment_type' => 'bank_transfer',
            'midtrans_transaction_status' => 'settlement',
            'order_status' => 'shipped',
            'tracking_number' => 'JN123456789ID',
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $watch->id,
            'product_name' => $watch->name,
            'sku' => $watch->sku,
            'quantity' => 1,
            'unit_price' => 2990000,
            'total_price' => 2990000,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $bag->id,
            'product_name' => $bag->name,
            'sku' => $bag->sku,
            'quantity' => 1,
            'unit_price' => 1200000,
            'total_price' => 1200000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order2->id,
            'status' => 'pending',
            'notes' => 'Pesanan guest berhasil dibuat.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order2->id,
            'status' => 'processing',
            'notes' => 'Pembayaran lunas via bank transfer.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order2->id,
            'status' => 'shipped',
            'notes' => 'Pesanan telah diserahkan ke kurir JNE. No Resi: JN123456789ID.',
            'changed_by' => $admin->id,
        ]);


        // --- Order 3: Pending, Status: Pending (Customer: Andi) ---
        $order3 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0003',
            'user_id' => $customer->id,
            'shipping_address' => $addressAndi,
            'subtotal' => 1200000, // Bag
            'shipping_cost' => 15000,
            'total_amount' => 1215000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'pending',
            'midtrans_transaction_id' => 'midtrans-tx-3333',
            'midtrans_payment_type' => 'bank_transfer',
            'midtrans_transaction_status' => 'pending',
            'order_status' => 'pending',
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $bag->id,
            'product_name' => $bag->name,
            'sku' => $bag->sku,
            'quantity' => 1,
            'unit_price' => 1200000,
            'total_price' => 1200000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order3->id,
            'status' => 'pending',
            'notes' => 'Menunggu pembayaran dari pelanggan.',
        ]);


        // --- Order 4: Failed, Status: Cancelled (Guest: Siti) ---
        $order4 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0004',
            'user_id' => null,
            'guest_name' => 'Siti Aminah',
            'guest_email' => 'siti@example.com',
            'guest_phone' => '+6285640001111',
            'shipping_address' => $addressSiti,
            'subtotal' => 590000, // Sweatshirt
            'shipping_cost' => 10000,
            'total_amount' => 600000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'failed',
            'midtrans_transaction_id' => 'midtrans-tx-4444',
            'midtrans_payment_type' => 'gopay',
            'midtrans_transaction_status' => 'deny',
            'order_status' => 'cancelled',
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $sweatshirt->id,
            'product_name' => $sweatshirt->name,
            'variant_name' => 'Black - M',
            'sku' => 'FA-SS-OCC-BLK-M',
            'quantity' => 1,
            'unit_price' => 590000,
            'total_price' => 590000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order4->id,
            'status' => 'pending',
            'notes' => 'Pesanan berhasil dibuat.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order4->id,
            'status' => 'cancelled',
            'notes' => 'Pembayaran ditolak/gagal via GoPay. Status dibatalkan secara otomatis.',
        ]);


        // --- Order 5: Paid, Status: Delivered (Customer: Andi) ---
        $order5 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-0005',
            'user_id' => $customer->id,
            'shipping_address' => $addressAndi,
            'subtotal' => 2990000, // Watch
            'shipping_cost' => 15000,
            'total_amount' => 3005000,
            'payment_method' => 'Midtrans Snap',
            'payment_status' => 'paid',
            'midtrans_transaction_id' => 'midtrans-tx-5555',
            'midtrans_payment_type' => 'credit_card',
            'midtrans_transaction_status' => 'capture',
            'order_status' => 'delivered',
            'tracking_number' => 'JN987654321ID',
            'notes' => 'Tolong kirim sebelum jam 5 sore.',
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $watch->id,
            'product_name' => $watch->name,
            'sku' => $watch->sku,
            'quantity' => 1,
            'unit_price' => 2990000,
            'total_price' => 2990000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order5->id,
            'status' => 'pending',
            'notes' => 'Pesanan berhasil dibuat.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order5->id,
            'status' => 'processing',
            'notes' => 'Pembayaran kartu kredit terverifikasi lunas.',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order5->id,
            'status' => 'shipped',
            'notes' => 'Pesanan dikirim via JNE. Resi: JN987654321ID.',
            'changed_by' => $admin->id,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order5->id,
            'status' => 'delivered',
            'notes' => 'Pesanan telah diterima oleh YBS (Andi Wijaya).',
            'changed_by' => $admin->id,
        ]);
    }
}

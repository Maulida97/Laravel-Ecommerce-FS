<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create attributes
        $colorAttr = VariantAttribute::create(['name' => 'Color']);
        $sizeAttr = VariantAttribute::create(['name' => 'Size']);

        // 2. Create attribute values
        $colors = [
            'Red' => '#EF4444',
            'Black' => '#1F2937',
            'Blue' => '#3B82F6',
            'White' => '#FFFFFF',
        ];
        $colorValues = [];
        foreach ($colors as $name => $code) {
            $colorValues[$name] = VariantAttributeValue::create([
                'variant_attribute_id' => $colorAttr->id,
                'value' => $name,
                'color_code' => $code,
            ]);
        }

        $sizes = ['S', 'M', 'L', 'XL'];
        $sizeValues = [];
        foreach ($sizes as $size) {
            $sizeValues[$size] = VariantAttributeValue::create([
                'variant_attribute_id' => $sizeAttr->id,
                'value' => $size,
            ]);
        }

        // Get Categories
        $electronics = Category::where('slug', 'electronics')->first();
        $fashion = Category::where('slug', 'fashion')->first();
        $accessories = Category::where('slug', 'accessories')->first();
        $beauty = Category::where('slug', 'beauty')->first();
        $home = Category::where('slug', 'home')->first();
        $sports = Category::where('slug', 'sports')->first();

        // 3. Products
        // Product 1: Wireless Headphones
        $headphone = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Wireless Noise-Canceling Headphones X1',
            'slug' => 'wireless-noise-canceling-headphones-x1',
            'description' => '<p>Immerse yourself in pure sound. Experience industry-leading noise canceling with our premium headphones, designed for exceptional comfort and up to 30 hours of battery life.</p>',
            'short_description' => 'Premium active noise-canceling headphones with 30h battery life.',
            'price' => 3499000,
            'compare_at_price' => 3999000,
            'sku' => 'EL-HP-WNCX1',
            'stock_quantity' => 3, // Low stock alert trigger
            'weight' => 250,
            'is_active' => true,
            'is_featured' => true,
            'meta_title' => 'Wireless Noise-Canceling Headphones X1 - Tokoku.id',
            'meta_description' => 'Shop premium noise-canceling wireless headphones at Tokoku.id',
        ]);
        ProductImage::create([
            'product_id' => $headphone->id,
            'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop',
            'public_id' => 'sample_headphone_primary',
            'alt_text' => 'Wireless Noise-Canceling Headphones X1',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Product 2: Smart Watch
        $watch = Product::create([
            'category_id' => $electronics->id,
            'name' => 'Smart Watch Series 5',
            'slug' => 'smart-watch-series-5',
            'description' => '<p>Track your health and stay connected. The Smart Watch Series 5 monitors your heart rate, sleep quality, and workouts while keeping notifications on your wrist.</p>',
            'short_description' => 'Stylish smart fitness watch with health tracking.',
            'price' => 2990000,
            'sku' => 'EL-SW-S5',
            'stock_quantity' => 2, // Low stock alert
            'weight' => 150,
            'is_active' => true,
            'is_featured' => true,
            'meta_title' => 'Smart Watch Series 5 - Tokoku.id',
            'meta_description' => 'Buy fitness smart watch with continuous tracking.',
        ]);
        ProductImage::create([
            'product_id' => $watch->id,
            'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&h=800&fit=crop',
            'public_id' => 'sample_watch_primary',
            'alt_text' => 'Smart Watch Series 5',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Product 3: Organic Sweatshirt
        $sweatshirt = Product::create([
            'category_id' => $fashion->id,
            'name' => 'Organic Cotton Crewneck Sweatshirt',
            'slug' => 'organic-cotton-crewneck-sweatshirt',
            'description' => '<p>Made from 100% organic cotton, this classic crewneck sweatshirt offers premium softness and sustainable comfort for everyday wear.</p>',
            'short_description' => '100% organic cotton crewneck sweatshirt.',
            'price' => 590000,
            'compare_at_price' => 790000,
            'sku' => 'FA-SS-OCC',
            'stock_quantity' => 50,
            'weight' => 400,
            'is_active' => true,
            'is_featured' => true,
        ]);
        ProductImage::create([
            'product_id' => $sweatshirt->id,
            'image_url' => 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=800&h=800&fit=crop',
            'public_id' => 'sample_sweatshirt_primary',
            'alt_text' => 'Organic Cotton Crewneck Sweatshirt',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Add variants for Sweatshirt
        $variant1 = ProductVariant::create([
            'product_id' => $sweatshirt->id,
            'variant_name' => 'Black - M',
            'sku' => 'FA-SS-OCC-BLK-M',
            'price_adjustment' => 0,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
        $variant1->combinations()->attach([$colorValues['Black']->id, $sizeValues['M']->id]);

        $variant2 = ProductVariant::create([
            'product_id' => $sweatshirt->id,
            'variant_name' => 'Red - L',
            'sku' => 'FA-SS-OCC-RED-L',
            'price_adjustment' => 50000, // Extra Rp 50.000 for red L
            'stock_quantity' => 15,
            'is_active' => true,
        ]);
        $variant2->combinations()->attach([$colorValues['Red']->id, $sizeValues['L']->id]);

        // Product 4: Leather Messenger Bag
        $bag = Product::create([
            'category_id' => $accessories->id,
            'name' => 'Vintage Leather Messenger Bag',
            'slug' => 'vintage-leather-messenger-bag',
            'description' => '<p>Crafted from full-grain leather, this messenger bag features a vintage style with spacious compartments to fit up to a 15-inch laptop and daily essentials.</p>',
            'short_description' => 'Full-grain vintage leather messenger bag.',
            'price' => 1200000,
            'sku' => 'AC-BG-VLM',
            'stock_quantity' => 12,
            'weight' => 1100,
            'is_active' => true,
            'is_featured' => true,
        ]);
        ProductImage::create([
            'product_id' => $bag->id,
            'image_url' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=800&h=800&fit=crop',
            'public_id' => 'sample_bag_primary',
            'alt_text' => 'Vintage Leather Messenger Bag',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Product 5: Running Shoes
        $shoes = Product::create([
            'category_id' => $sports->id,
            'name' => 'Running Shoes Red',
            'slug' => 'running-shoes-red',
            'description' => '<p>Designed for daily trainers. Lightweight foam cushioning offers responsive rebound, and breathable mesh fabric keeps you cool during long runs.</p>',
            'short_description' => 'Comfortable mesh running shoes with responsive cushioning.',
            'price' => 890000,
            'compare_at_price' => 1100000,
            'sku' => 'SP-SH-RSR',
            'stock_quantity' => 8,
            'weight' => 600,
            'is_active' => true,
            'is_featured' => false,
        ]);
        ProductImage::create([
            'product_id' => $shoes->id,
            'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&h=800&fit=crop',
            'public_id' => 'sample_shoes_primary',
            'alt_text' => 'Running Shoes Red',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        // Product 6: Sunglasses
        $sunglasses = Product::create([
            'category_id' => $accessories->id,
            'name' => 'Sunglasses Classic',
            'slug' => 'sunglasses-classic',
            'description' => '<p>Classic style with full UV400 protection. Premium metal frames offer a lightweight feel and durable performance.</p>',
            'short_description' => 'Classic frame sunglasses with UV protection.',
            'price' => 450000,
            'sku' => 'AC-SG-CLC',
            'stock_quantity' => 6,
            'weight' => 50,
            'is_active' => true,
            'is_featured' => false,
        ]);
        ProductImage::create([
            'product_id' => $sunglasses->id,
            'image_url' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&h=800&fit=crop',
            'public_id' => 'sample_sunglasses_primary',
            'alt_text' => 'Sunglasses Classic',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest gadgets, smart watches, and audio equipment.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop',
                'sort_order' => 1,
            ],
            [
                'name' => 'Fashion',
                'description' => 'Apparel, organic cotton clothing, and seasonal trends.',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=400&h=400&fit=crop',
                'sort_order' => 2,
            ],
            [
                'name' => 'Accessories',
                'description' => 'Bags, sunglasses, wallets, and premium everyday gear.',
                'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=400&fit=crop',
                'sort_order' => 3,
            ],
            [
                'name' => 'Beauty',
                'description' => 'Skincare, premium cosmetics, and personal wellness.',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
                'sort_order' => 4,
            ],
            [
                'name' => 'Home',
                'description' => 'Elevate your interior styling with minimal design goods.',
                'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=400&h=400&fit=crop',
                'sort_order' => 5,
            ],
            [
                'name' => 'Sports',
                'description' => 'Premium sportswear, yoga mats, and outdoor equipment.',
                'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=400&h=400&fit=crop',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'image' => $cat['image'],
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => $cat['sort_order'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Discover Premium Products for Your Lifestyle',
                'subtitle' => 'Curated selection of high-quality products designed to elevate your everyday experience. Shop the latest trends with confidence.',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=600&fit=crop',
                'button_text' => 'Explore Collection',
                'button_link' => '#products',
                'sort_order' => 1,
            ],
            [
                'title' => 'Mid Season Sale — Up to 50% Off',
                'subtitle' => 'Unmissable deals on organic sweatshirts, shoes, and timeless leather bags. Limited time offer, free shipping included.',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&h=600&fit=crop',
                'button_text' => 'Shop Sale Now',
                'button_link' => '#products',
                'sort_order' => 2,
            ],
            [
                'title' => 'Stay Ahead with Smart Accessories',
                'subtitle' => 'Browse our latest fitness smartwatches and noise-canceling headphones designed for tech enthusiasts.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1200&h=600&fit=crop',
                'button_text' => 'Shop Smart Devices',
                'button_link' => '#products',
                'sort_order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            Slide::create($slide);
        }
    }
}

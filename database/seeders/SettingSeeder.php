<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'store_name' => 'Tokoku.id',
            'store_tagline' => 'Premium E-Commerce Platform',
            'contact_email' => 'support@tokoku.id',
            'contact_phone' => '+6281234567890',
            'contact_address' => 'Jl. Premium No. 1, Jakarta, Indonesia',
            'default_shipping_cost' => '15000.00',
            'free_shipping_threshold' => '500000.00',
            'social_twitter' => 'https://twitter.com/tokoku',
            'social_instagram' => 'https://instagram.com/tokoku',
            'social_facebook' => 'https://facebook.com/tokoku',
            'midtrans_sandbox_mode' => 'true',
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}

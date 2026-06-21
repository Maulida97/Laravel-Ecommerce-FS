<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed global settings, categories, products, slides
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            SlideSeeder::class,
            SettingSeeder::class,
        ]);

        // 2. Create Default Admin User
        User::create([
            'name' => 'Admin Tokoku',
            'email' => 'admin@tokoku.id',
            'password' => 'password', // will be hashed automatically by User model casts
            'role' => 'admin',
            'phone' => '+628123456789',
            'address' => 'Headquarters, Jakarta',
        ]);

        // 3. Create Default Customer User
        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@example.com',
            'password' => 'password',
            'role' => 'customer',
            'phone' => '+628777665544',
            'address' => 'Jl. Kebagusan Raya No. 45, Jakarta Selatan',
        ]);

        // 4. Seed Orders (requires users to be created first)
        $this->call([
            OrderSeeder::class,
        ]);
    }
}

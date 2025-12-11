<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            CategorySeeder::class, // Cần cho ProductSeeder
            ColorSeeder::class, // Cần cho ProductSeeder
            SizeSeeder::class, // Cần cho ProductSeeder
            TextureSeeder::class, // Cần cho ProductSeeder
            ProductSeeder::class, // Cần categories, colors, sizes, textures
            ProductImageSeeder::class, // Cần products
            LoyaltyTierSeeder::class,
            WarehouseSeeder::class,
            ShippingCarrierSeeder::class,
            TaxRateSeeder::class,
            BannerSeeder::class,
            PostSeeder::class,
            TagSeeder::class,
            VoucherSeeder::class,
            RoleSeeder::class, // Phải chạy trước PermissionSeeder
            PermissionSeeder::class,
        ]);
    }
}

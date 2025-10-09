<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo/cập nhật user cố định, tránh trùng email khi seed nhiều lần
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // đổi nếu cần
            ]
        );

        $this->call([
            TaxRateSeeder::class,
            ShippingCarrierSeeder::class,
        ]);
    }
}

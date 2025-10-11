<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🧑‍💼 1. Tạo admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone_number' => '0900000001',
                'is_admin' => 1,
                'verification_token' => Str::random(32),
                'token_expires_at' => now()->addDays(7),
                'is_verified' => 1,
            ]
        );

        // 👥 2. Tạo 4 người dùng thường
        for ($i = 1; $i <= 4; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => fake()->name(),
                    'password' => Hash::make('password'),
                    'email_verified_at' => fake()->boolean(70) ? now() : null,
                    'phone_number' => '090000000' . ($i + 1),
                    'is_admin' => 0,
                    'verification_token' => Str::random(32),
                    'token_expires_at' => now()->addDays(rand(3, 10)),
                    'is_verified' => fake()->boolean(60) ? 1 : 0,
                ]
            );
        }
    }
}

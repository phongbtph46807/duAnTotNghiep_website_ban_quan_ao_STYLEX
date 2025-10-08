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
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_number' => '0900000001',
            'avatar' => 'https://ui-avatars.com/api/?name=Admin+User',
            'status' => 'active',
            'is_admin' => 1,
            'verification_token' => Str::random(32),
            'token_expires_at' => now()->addDays(7),
            'is_verified' => 1,
            'salary' => 25000000,
            'hire_date' => '2022-01-10',
        ]);

        // 👥 2. Tạo 4 người dùng thường
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => fake()->boolean(70) ? now() : null,
                'phone_number' => '090000000' . ($i + 1),
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode(fake()->name()),
                'status' => fake()->randomElement(['active', 'inactive']),
                'is_admin' => 0,
                'verification_token' => Str::random(32),
                'token_expires_at' => now()->addDays(rand(3, 10)),
                'is_verified' => fake()->boolean(60) ? 1 : 0,
                'salary' => fake()->randomFloat(2, 8000000, 20000000),
                'hire_date' => fake()->dateTimeBetween('-3 years', 'now'),
            ]);
        }
    }
}

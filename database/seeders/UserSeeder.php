<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa tất cả users hiện có (không dùng truncate vì có foreign key)
        User::query()->delete();

        // Tạo Admin
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'),
                'role' => 1,
                'is_admin' => 1,
                'email_verified_at' => now()
            ]
        );

        // Tạo Staff
        User::updateOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('123456'),
                'role' => 2,
                'is_admin' => 0,
                'email_verified_at' => now()
            ]
        );

        // Tạo User thường
        User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('123456'),
                'role' => 0,
                'is_admin' => 0,
                'email_verified_at' => now()
            ]
        );

        $this->command->info('Created test users:');
        $this->command->info('Admin: admin@test.com / 123456 (role=1)');
        $this->command->info('Staff: staff@test.com / 123456 (role=2)');
        $this->command->info('User: user@test.com / 123456 (role=0)');
    }
}
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Đảm bảo import Model User

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Admin (Đã xác minh)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(), // Đặt thời gian xác minh
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_verified' => true, // Đặt là đã xác minh
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        // 2. Tạo tài khoản người dùng thông thường (Chưa xác minh)
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'email_verified_at' => null, // Hoặc không cần thiết lập
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_verified' => false, // Đặt là chưa xác minh
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        // 3. Tạo thêm 2 người dùng ngẫu nhiên
        User::factory(2)->create();
    }
}

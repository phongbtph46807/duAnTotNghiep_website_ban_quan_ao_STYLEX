<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo các role cơ bản với màu
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Quản lý toàn bộ website',
                'color' => 'danger' // Đỏ - quan trọng nhất
            ],
            [
                'name' => 'Staff',
                'description' => 'Nhân viên quản lý sản phẩm và đơn hàng',
                'color' => 'primary' // Xanh dương
            ],
            [
                'name' => 'Warehouse Manager',
                'description' => 'Quản lý kho hàng và tồn kho',
                'color' => 'info' // Xanh nhạt
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Đã tạo ' . count($roles) . ' roles.');
    }
}


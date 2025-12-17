<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'id' => 1]);
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'id' => 2]);
        $warehouseRole = Role::firstOrCreate(['name' => 'Warehouse Manager', 'id' => 3]);

        // Tạo Permissions
        $permissions = [
            // Sản phẩm
            'products.view' => 'Xem sản phẩm',
            'products.create' => 'Tạo sản phẩm',
            'products.edit' => 'Sửa sản phẩm',
            'products.delete' => 'Xóa sản phẩm',

            // Danh mục
            'categories.view' => 'Xem danh mục',
            'categories.create' => 'Tạo danh mục',
            'categories.edit' => 'Sửa danh mục',
            'categories.delete' => 'Xóa danh mục',

            // Bài viết
            'posts.view' => 'Xem bài viết',
            'posts.create' => 'Tạo bài viết',
            'posts.edit' => 'Sửa bài viết',
            'posts.delete' => 'Xóa bài viết',

            // Banner
            'banners.view' => 'Xem banner',
            'banners.create' => 'Tạo banner',
            'banners.edit' => 'Sửa banner',
            'banners.delete' => 'Xóa banner',

            // Đánh giá
            'reviews.view' => 'Xem đánh giá',
            'reviews.edit' => 'Sửa đánh giá',
            'reviews.delete' => 'Xóa đánh giá',

            // Kho hàng
            'inventory.view' => 'Xem kho hàng',
            'inventory.stock-in' => 'Nhập kho',
            'inventory.stock-out' => 'Xuất kho',
            'inventory.transfer' => 'Chuyển kho',
            'inventory.count' => 'Kiểm kê',
            'inventory.defect' => 'Đánh giá hàng hỏng',
            'inventory.returns' => 'Trả/Đổi hàng',
            'inventory.warehouse' => 'Quản lý kho',

            // Đơn hàng
            'orders.view' => 'Xem đơn hàng',
            'orders.edit' => 'Sửa đơn hàng',
            'orders.fulfillment' => 'Quản lý fulfillment',

            // Người dùng
            'users.view' => 'Xem người dùng',
            'users.create' => 'Tạo người dùng',
            'users.edit' => 'Sửa người dùng',
            'users.delete' => 'Xóa người dùng',

            // Vai trò & Quyền
            'roles.manage' => 'Quản lý vai trò',
            'permissions.manage' => 'Quản lý quyền',

            // Cấu hình
            'settings.manage' => 'Quản lý cấu hình',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['description' => $description]);
        }

        // Gán quyền cho Admin (role=1) - Toàn bộ quyền
        $adminPermissions = Permission::all()->pluck('id')->toArray();
        $adminRole->permissions()->sync($adminPermissions);

        // Gán quyền cho Staff (role=2) - Sản phẩm, danh mục, bài viết, banner, đánh giá, kho hàng
        $staffPermissions = Permission::whereIn('name', [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete',
            'banners.view', 'banners.create', 'banners.edit', 'banners.delete',
            'reviews.view', 'reviews.edit', 'reviews.delete',
            'inventory.view', 'inventory.stock-in', 'inventory.stock-out', 'inventory.transfer',
            'inventory.count', 'inventory.defect', 'inventory.returns', 'inventory.warehouse',
            'users.view', 'users.edit',
        ])->pluck('id')->toArray();
        $staffRole->permissions()->sync($staffPermissions);

        // Gán quyền cho Warehouse Manager (role=3) - Chỉ kho hàng
        $warehousePermissions = Permission::whereIn('name', [
            'inventory.view', 'inventory.stock-in', 'inventory.stock-out', 'inventory.transfer',
            'inventory.count', 'inventory.defect', 'inventory.returns', 'inventory.warehouse',
        ])->pluck('id')->toArray();
        $warehouseRole->permissions()->sync($warehousePermissions);
    }
}

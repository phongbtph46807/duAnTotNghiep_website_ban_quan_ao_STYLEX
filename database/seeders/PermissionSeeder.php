<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo roles trước
        $roles = [
            'Admin' => 'Quản trị viên - toàn quyền',
            'Staff' => 'Nhân viên - quyền hạn chế',
            'Warehouse Manager' => 'Quản lý kho - chỉ quản lý kho hàng',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        // Định nghĩa các quyền
        $permissions = [
            // Dashboard
            'admin.dashboard' => 'Xem dashboard admin',

            // Products
            'admin.products.index' => 'Xem danh sách sản phẩm',
            'admin.products.create' => 'Tạo sản phẩm mới',
            'admin.products.edit' => 'Sửa sản phẩm',
            'admin.products.delete' => 'Xóa sản phẩm',

            // Categories
            'admin.categories.index' => 'Xem danh mục',
            'admin.categories.create' => 'Tạo danh mục',
            'admin.categories.edit' => 'Sửa danh mục',
            'admin.categories.delete' => 'Xóa danh mục',

            // Orders
            'admin.orders.index' => 'Xem đơn hàng',
            'admin.orders.show' => 'Xem chi tiết đơn hàng',
            'admin.orders.confirm' => 'Xác nhận đơn hàng',
            'admin.orders.ship' => 'Vận chuyển đơn hàng',
            'admin.orders.cancel' => 'Hủy đơn hàng',

            // Users
            'admin.users.index' => 'Xem danh sách người dùng',
            'admin.users.create' => 'Tạo người dùng',
            'admin.users.edit' => 'Sửa người dùng',
            'admin.users.delete' => 'Xóa người dùng',

            // Inventory - Dashboard
            'admin.inventory.index' => 'Trang chủ quản lý kho',
            'admin.inventory.dashboard' => 'Dashboard kho',
            'admin.inventory.current-stock' => 'Xem tồn kho hiện tại',
            'admin.inventory.reports' => 'Báo cáo kho',
            'admin.inventory.logs' => 'Lịch sử giao dịch kho',
            'admin.inventory.settings' => 'Cài đặt kho',
            'admin.inventory.settings.update' => 'Cập nhật cài đặt kho',

            // Inventory - Stock Operations
            'admin.inventory.stock-in.index' => 'Xem danh sách nhập kho',
            'admin.inventory.stock-in.create' => 'Tạo phiếu nhập kho',
            'admin.inventory.stock-in.qc' => 'Kiểm tra chất lượng nhập kho',
            'admin.inventory.stock-in.confirm-qc' => 'Xác nhận QC nhập kho',
            'admin.inventory.stock-in.confirm' => 'Xác nhận nhập kho',
            'admin.inventory.stock-in.reject' => 'Từ chối nhập kho',
            
            'admin.inventory.stock-out.index' => 'Xem danh sách xuất kho',
            'admin.inventory.stock-out.create' => 'Tạo phiếu xuất kho',
            'admin.inventory.stock-out.qc' => 'Kiểm tra chất lượng xuất kho',
            'admin.inventory.stock-out.confirm-qc' => 'Xác nhận QC xuất kho',
            'admin.inventory.stock-out.confirm' => 'Xác nhận xuất kho',
            'admin.inventory.stock-out.reject' => 'Từ chối xuất kho',

            // Inventory - Transfer
            'admin.inventory.transfer.index' => 'Xem chuyển kho',
            'admin.inventory.transfer.create' => 'Tạo phiếu chuyển kho',
            'admin.inventory.transfer.confirm-out' => 'Xác nhận xuất chuyển kho',
            'admin.inventory.transfer.confirm-in' => 'Xác nhận nhập chuyển kho',

            // Inventory - Count
            'admin.inventory.count.index' => 'Xem kiểm kê',
            'admin.inventory.count.create' => 'Tạo phiếu kiểm kê',
            'admin.inventory.count.count' => 'Thực hiện kiểm kê',
            'admin.inventory.count.confirm-count' => 'Xác nhận kết quả kiểm kê',
            'admin.inventory.count.confirm-adjustment' => 'Xác nhận điều chỉnh kiểm kê',

            // Inventory - Defect
            'admin.inventory.defect.index' => 'Xem hàng hỏng',
            'admin.inventory.defect.create' => 'Tạo báo cáo hàng hỏng',
            'admin.inventory.defect.assess' => 'Đánh giá hàng hỏng',
            'admin.inventory.defect.confirm-assess' => 'Xác nhận đánh giá hàng hỏng',
            'admin.inventory.defect.approve' => 'Phê duyệt xử lý hàng hỏng',
            'admin.inventory.defect.complete' => 'Hoàn thành xử lý hàng hỏng',
            'admin.inventory.defect.reject' => 'Từ chối xử lý hàng hỏng',



            // Inventory - Stock Out Invoice
            'admin.inventory.stock-out-invoice.index' => 'Xem hóa đơn thanh lý',
            'admin.inventory.stock-out-invoice.show' => 'Xem chi tiết hóa đơn thanh lý',
            'admin.inventory.stock-out-invoice.complete' => 'Hoàn thành hóa đơn thanh lý',

            // Inventory - Warehouses
            'admin.inventory.warehouses.index' => 'Quản lý kho hàng',
            'admin.inventory.warehouses.create' => 'Tạo kho mới',
            'admin.inventory.warehouses.store' => 'Lưu kho mới',
            'admin.inventory.warehouses.show' => 'Xem chi tiết kho hàng',
            'admin.inventory.warehouses.edit' => 'Sửa thông tin kho',
            'admin.inventory.warehouses.update' => 'Cập nhật thông tin kho',
            'admin.inventory.warehouses.destroy' => 'Xóa kho hàng',

            // Notifications
            'admin.notifications.index' => 'Xem thông báo',

            // System
            'admin.roles.index' => 'Quản lý phân quyền',
            'admin.vouchers.index' => 'Quản lý voucher',
            'admin.loyalty-tiers.index' => 'Quản lý hạng thành viên',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        // Gán quyền cho Admin - toàn quyền
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::all());
        }

        // Gán quyền cho Staff - sản phẩm và đơn hàng
        $staffRole = Role::where('name', 'Staff')->first();
        if ($staffRole) {
            $staffPermissions = Permission::whereIn('name', [
                'admin.dashboard',
                'admin.products.index',
                'admin.products.create',
                'admin.products.edit',
                'admin.categories.index',
                'admin.orders.index',
                'admin.orders.show',
                'admin.orders.confirm',
            ])->get();
            
            $staffRole->permissions()->sync($staffPermissions);
        }

        // Gán quyền cho Warehouse Manager - chỉ quản lý kho
        $warehouseRole = Role::where('name', 'Warehouse Manager')->first();
        if ($warehouseRole) {
            $warehousePermissions = Permission::where('name', 'LIKE', 'admin.inventory.%')
                ->orWhere('name', 'admin.dashboard')
                ->orWhere('name', 'admin.notifications.index')
                ->get();
            
            $warehouseRole->permissions()->sync($warehousePermissions);
        }
    }
}

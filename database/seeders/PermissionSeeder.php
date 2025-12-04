<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'admin.dashboard',
                'description' => 'Xem trang tổng quan'
            ],

            // Categories
            [
                'name' => 'admin.categories.index',
                'description' => 'Xem danh sách danh mục'
            ],
            [
                'name' => 'admin.category.store',
                'description' => 'Tạo danh mục mới'
            ],
            [
                'name' => 'admin.category.edit',
                'description' => 'Xem form chỉnh sửa danh mục'
            ],
            [
                'name' => 'admin.category.update',
                'description' => 'Cập nhật danh mục'
            ],
            [
                'name' => 'admin.category.destroy',
                'description' => 'Xóa danh mục'
            ],

            // Colors
            [
                'name' => 'admin.colors.index',
                'description' => 'Xem danh sách màu sắc'
            ],
            [
                'name' => 'admin.colors.create',
                'description' => 'Tạo màu sắc mới'
            ],
            [
                'name' => 'admin.colors.store',
                'description' => 'Lưu màu sắc mới'
            ],
            [
                'name' => 'admin.colors.show',
                'description' => 'Xem chi tiết màu sắc'
            ],
            [
                'name' => 'admin.colors.edit',
                'description' => 'Chỉnh sửa màu sắc'
            ],
            [
                'name' => 'admin.colors.update',
                'description' => 'Cập nhật màu sắc'
            ],
            [
                'name' => 'admin.colors.destroy',
                'description' => 'Xóa màu sắc'
            ],

            // Sizes
            [
                'name' => 'admin.sizes.index',
                'description' => 'Xem danh sách kích thước'
            ],
            [
                'name' => 'admin.sizes.create',
                'description' => 'Tạo kích thước mới'
            ],
            [
                'name' => 'admin.sizes.store',
                'description' => 'Lưu kích thước mới'
            ],
            [
                'name' => 'admin.sizes.show',
                'description' => 'Xem chi tiết kích thước'
            ],
            [
                'name' => 'admin.sizes.edit',
                'description' => 'Chỉnh sửa kích thước'
            ],
            [
                'name' => 'admin.sizes.update',
                'description' => 'Cập nhật kích thước'
            ],
            [
                'name' => 'admin.sizes.destroy',
                'description' => 'Xóa kích thước'
            ],

            // Textures
            [
                'name' => 'admin.textures.index',
                'description' => 'Xem danh sách chất liệu'
            ],
            [
                'name' => 'admin.textures.create',
                'description' => 'Tạo chất liệu mới'
            ],
            [
                'name' => 'admin.textures.store',
                'description' => 'Lưu chất liệu mới'
            ],
            [
                'name' => 'admin.textures.show',
                'description' => 'Xem chi tiết chất liệu'
            ],
            [
                'name' => 'admin.textures.edit',
                'description' => 'Chỉnh sửa chất liệu'
            ],
            [
                'name' => 'admin.textures.update',
                'description' => 'Cập nhật chất liệu'
            ],
            [
                'name' => 'admin.textures.destroy',
                'description' => 'Xóa chất liệu'
            ],

            // Products
            [
                'name' => 'admin.products.index',
                'description' => 'Xem danh sách sản phẩm'
            ],
            [
                'name' => 'admin.products.trash',
                'description' => 'Xem sản phẩm đã xóa'
            ],
            [
                'name' => 'admin.products.create',
                'description' => 'Tạo sản phẩm mới'
            ],
            [
                'name' => 'admin.products.show',
                'description' => 'Xem chi tiết sản phẩm'
            ],
            [
                'name' => 'admin.products.store',
                'description' => 'Lưu sản phẩm mới'
            ],
            [
                'name' => 'admin.products.filter',
                'description' => 'Lọc sản phẩm'
            ],
            [
                'name' => 'admin.products.edit',
                'description' => 'Chỉnh sửa sản phẩm'
            ],
            [
                'name' => 'admin.products.update',
                'description' => 'Cập nhật sản phẩm'
            ],
            [
                'name' => 'admin.products.destroy',
                'description' => 'Xóa sản phẩm'
            ],
            [
                'name' => 'admin.products.toggleFeature',
                'description' => 'Bật/tắt sản phẩm nổi bật'
            ],
            [
                'name' => 'admin.products.restore',
                'description' => 'Khôi phục sản phẩm'
            ],
            [
                'name' => 'admin.products.force-delete',
                'description' => 'Xóa vĩnh viễn sản phẩm'
            ],
            [
                'name' => 'admin.products.images.store',
                'description' => 'Thêm ảnh sản phẩm'
            ],
            [
                'name' => 'admin.variants.images.store',
                'description' => 'Thêm ảnh biến thể sản phẩm'
            ],

            // Profile
            [
                'name' => 'admin.profile',
                'description' => 'Xem thông tin cá nhân'
            ],
            [
                'name' => 'admin.profile.edit',
                'description' => 'Chỉnh sửa thông tin cá nhân'
            ],
            [
                'name' => 'admin.profile.update',
                'description' => 'Cập nhật thông tin cá nhân'
            ],

            // Banners
            [
                'name' => 'admin.banners.index',
                'description' => 'Xem danh sách banner'
            ],
            [
                'name' => 'admin.banners.trash',
                'description' => 'Xem banner đã xóa'
            ],
            [
                'name' => 'admin.banners.create',
                'description' => 'Tạo banner mới'
            ],
            [
                'name' => 'admin.banners.show',
                'description' => 'Xem chi tiết banner'
            ],
            [
                'name' => 'admin.banners.store',
                'description' => 'Lưu banner mới'
            ],
            [
                'name' => 'admin.banners.updateOrder',
                'description' => 'Cập nhật thứ tự banner'
            ],
            [
                'name' => 'admin.banners.edit',
                'description' => 'Chỉnh sửa banner'
            ],
            [
                'name' => 'admin.banners.update',
                'description' => 'Cập nhật banner'
            ],
            [
                'name' => 'admin.banners.destroy',
                'description' => 'Xóa banner'
            ],
            [
                'name' => 'admin.banners.restore',
                'description' => 'Khôi phục banner'
            ],
            [
                'name' => 'admin.banners.force-delete',
                'description' => 'Xóa vĩnh viễn banner'
            ],

            // Posts
            [
                'name' => 'admin.posts.index',
                'description' => 'Xem danh sách bài viết'
            ],
            [
                'name' => 'admin.posts.trash',
                'description' => 'Xem bài viết đã xóa'
            ],
            [
                'name' => 'admin.posts.create',
                'description' => 'Tạo bài viết mới'
            ],
            [
                'name' => 'admin.posts.show',
                'description' => 'Xem chi tiết bài viết'
            ],
            [
                'name' => 'admin.posts.store',
                'description' => 'Lưu bài viết mới'
            ],
            [
                'name' => 'admin.posts.edit',
                'description' => 'Chỉnh sửa bài viết'
            ],
            [
                'name' => 'admin.posts.update',
                'description' => 'Cập nhật bài viết'
            ],
            [
                'name' => 'admin.posts.destroy',
                'description' => 'Xóa bài viết'
            ],
            [
                'name' => 'admin.posts.restore',
                'description' => 'Khôi phục bài viết'
            ],
            [
                'name' => 'admin.posts.force-delete',
                'description' => 'Xóa vĩnh viễn bài viết'
            ],

            // Reviews
            [
                'name' => 'admin.reviews.index',
                'description' => 'Xem danh sách đánh giá'
            ],
            [
                'name' => 'admin.reviews.show',
                'description' => 'Xem chi tiết đánh giá'
            ],
            [
                'name' => 'admin.reviews.toggleStatus',
                'description' => 'Bật/tắt trạng thái đánh giá'
            ],
            [
                'name' => 'admin.reviews.destroy',
                'description' => 'Xóa đánh giá'
            ],

            // Users (Admin & Staff)
            [
                'name' => 'admin.users.index',
                'description' => 'Xem danh sách người dùng'
            ],
            [
                'name' => 'admin.users.edit',
                'description' => 'Chỉnh sửa người dùng'
            ],
            [
                'name' => 'admin.users.update',
                'description' => 'Cập nhật người dùng'
            ],

            // Users (Admin only)
            [
                'name' => 'admin.users.trash',
                'description' => 'Xem người dùng đã xóa'
            ],
            [
                'name' => 'admin.users.create',
                'description' => 'Tạo người dùng mới'
            ],
            [
                'name' => 'admin.users.show',
                'description' => 'Xem chi tiết người dùng'
            ],
            [
                'name' => 'admin.users.store',
                'description' => 'Lưu người dùng mới'
            ],
            [
                'name' => 'admin.users.filter',
                'description' => 'Lọc người dùng'
            ],
            [
                'name' => 'admin.users.destroy',
                'description' => 'Xóa người dùng'
            ],
            [
                'name' => 'admin.users.updateEmailVerified',
                'description' => 'Cập nhật xác thực email'
            ],
            [
                'name' => 'admin.users.restore',
                'description' => 'Khôi phục người dùng'
            ],
            [
                'name' => 'admin.users.force-delete',
                'description' => 'Xóa vĩnh viễn người dùng'
            ],

            // Roles (User Role Management)
            [
                'name' => 'admin.roles.index',
                'description' => 'Xem danh sách vai trò người dùng'
            ],
            [
                'name' => 'admin.roles.create',
                'description' => 'Tạo vai trò người dùng mới'
            ],
            [
                'name' => 'admin.roles.store',
                'description' => 'Lưu vai trò người dùng mới'
            ],
            [
                'name' => 'admin.roles.edit',
                'description' => 'Chỉnh sửa vai trò người dùng'
            ],
            [
                'name' => 'admin.roles.update',
                'description' => 'Cập nhật vai trò người dùng'
            ],
            [
                'name' => 'admin.roles.destroy',
                'description' => 'Xóa vai trò người dùng'
            ],
            [
                'name' => 'admin.roles.check-admin-count',
                'description' => 'Kiểm tra số lượng admin'
            ],
            [
                'name' => 'admin.roles.update-role',
                'description' => 'Cập nhật vai trò cho người dùng'
            ],
            [
                'name' => 'admin.roles.bulk-update',
                'description' => 'Cập nhật hàng loạt vai trò'
            ],

            // Loyalty Tiers
            [
                'name' => 'admin.loyalty-tiers.index',
                'description' => 'Xem danh sách hạng thành viên'
            ],
            [
                'name' => 'admin.loyalty-tiers.create',
                'description' => 'Tạo hạng thành viên mới'
            ],
            [
                'name' => 'admin.loyalty-tiers.store',
                'description' => 'Lưu hạng thành viên mới'
            ],
            [
                'name' => 'admin.loyalty-tiers.show',
                'description' => 'Xem chi tiết hạng thành viên'
            ],
            [
                'name' => 'admin.loyalty-tiers.edit',
                'description' => 'Chỉnh sửa hạng thành viên'
            ],
            [
                'name' => 'admin.loyalty-tiers.update',
                'description' => 'Cập nhật hạng thành viên'
            ],
            [
                'name' => 'admin.loyalty-tiers.destroy',
                'description' => 'Xóa hạng thành viên'
            ],

            // Tax Rates
            [
                'name' => 'admin.tax_rates.index',
                'description' => 'Xem danh sách thuế suất'
            ],
            [
                'name' => 'admin.tax_rates.create',
                'description' => 'Tạo thuế suất mới'
            ],
            [
                'name' => 'admin.tax_rates.store',
                'description' => 'Lưu thuế suất mới'
            ],
            [
                'name' => 'admin.tax_rates.show',
                'description' => 'Xem chi tiết thuế suất'
            ],
            [
                'name' => 'admin.tax_rates.edit',
                'description' => 'Chỉnh sửa thuế suất'
            ],
            [
                'name' => 'admin.tax_rates.update',
                'description' => 'Cập nhật thuế suất'
            ],
            [
                'name' => 'admin.tax_rates.destroy',
                'description' => 'Xóa thuế suất'
            ],

            // Shipping Carriers
            [
                'name' => 'admin.shipping_carriers.index',
                'description' => 'Xem danh sách đơn vị vận chuyển'
            ],
            [
                'name' => 'admin.shipping_carriers.create',
                'description' => 'Tạo đơn vị vận chuyển mới'
            ],
            [
                'name' => 'admin.shipping_carriers.store',
                'description' => 'Lưu đơn vị vận chuyển mới'
            ],
            [
                'name' => 'admin.shipping_carriers.show',
                'description' => 'Xem chi tiết đơn vị vận chuyển'
            ],
            [
                'name' => 'admin.shipping_carriers.edit',
                'description' => 'Chỉnh sửa đơn vị vận chuyển'
            ],
            [
                'name' => 'admin.shipping_carriers.update',
                'description' => 'Cập nhật đơn vị vận chuyển'
            ],
            [
                'name' => 'admin.shipping_carriers.destroy',
                'description' => 'Xóa đơn vị vận chuyển'
            ],

            // Vouchers
            [
                'name' => 'admin.vouchers.index',
                'description' => 'Xem danh sách mã giảm giá'
            ],
            [
                'name' => 'admin.vouchers.create',
                'description' => 'Tạo mã giảm giá mới'
            ],
            [
                'name' => 'admin.vouchers.store',
                'description' => 'Lưu mã giảm giá mới'
            ],
            [
                'name' => 'admin.vouchers.show',
                'description' => 'Xem chi tiết mã giảm giá'
            ],
            [
                'name' => 'admin.vouchers.edit',
                'description' => 'Chỉnh sửa mã giảm giá'
            ],
            [
                'name' => 'admin.vouchers.update',
                'description' => 'Cập nhật mã giảm giá'
            ],
            [
                'name' => 'admin.vouchers.destroy',
                'description' => 'Xóa mã giảm giá'
            ],

            // RBAC - Roles Entity
            [
                'name' => 'admin.rbac.roles.index',
                'description' => 'Xem danh sách role'
            ],
            [
                'name' => 'admin.rbac.roles.create',
                'description' => 'Tạo role mới'
            ],
            [
                'name' => 'admin.rbac.roles.store',
                'description' => 'Lưu role mới'
            ],
            [
                'name' => 'admin.rbac.roles.edit',
                'description' => 'Chỉnh sửa role'
            ],
            [
                'name' => 'admin.rbac.roles.update',
                'description' => 'Cập nhật role'
            ],
            [
                'name' => 'admin.rbac.roles.destroy',
                'description' => 'Xóa role'
            ],

            // RBAC - Permissions Entity
            [
                'name' => 'admin.rbac.permissions.index',
                'description' => 'Xem danh sách permission'
            ],
            [
                'name' => 'admin.rbac.permissions.create',
                'description' => 'Tạo permission mới'
            ],
            [
                'name' => 'admin.rbac.permissions.store',
                'description' => 'Lưu permission mới'
            ],
            [
                'name' => 'admin.rbac.permissions.edit',
                'description' => 'Chỉnh sửa permission'
            ],
            [
                'name' => 'admin.rbac.permissions.update',
                'description' => 'Cập nhật permission'
            ],
            [
                'name' => 'admin.rbac.permissions.destroy',
                'description' => 'Xóa permission'
            ],

            // Orders
            [
                'name' => 'admin.orders.index',
                'description' => 'Xem danh sách đơn hàng'
            ],
            [
                'name' => 'admin.orders.updateStatus',
                'description' => 'Cập nhật trạng thái đơn hàng'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Đã tạo/cập nhật ' . count($permissions) . ' permissions.');

        // Cập nhật mô tả cho role Admin
        $adminRole = Role::where('name', 'Admin')->first();
        
        if ($adminRole) {
            $adminRole->update(['description' => 'Quản lý toàn bộ website']);
            $this->command->info("Đã cập nhật mô tả cho role Admin: 'Quản lý toàn bộ website'");
        } else {
            $this->command->warn('Không tìm thấy role Admin!');
        }

        // Gán quyền cho role Staff
        $staffRole = Role::where('name', 'Staff')->first();
        
        if ($staffRole) {
            // Các quyền mà Staff có thể truy cập (dựa trên routes checkRole:1,2)
            $staffPermissions = [
                // Dashboard
                'admin.dashboard',
                
                // Categories
                'admin.categories.index',
                'admin.category.store',
                'admin.category.edit',
                'admin.category.update',
                'admin.category.destroy',
                
                // Colors
                'admin.colors.index',
                'admin.colors.create',
                'admin.colors.store',
                'admin.colors.show',
                'admin.colors.edit',
                'admin.colors.update',
                'admin.colors.destroy',
                
                // Sizes
                'admin.sizes.index',
                'admin.sizes.create',
                'admin.sizes.store',
                'admin.sizes.show',
                'admin.sizes.edit',
                'admin.sizes.update',
                'admin.sizes.destroy',
                
                // Textures
                'admin.textures.index',
                'admin.textures.create',
                'admin.textures.store',
                'admin.textures.show',
                'admin.textures.edit',
                'admin.textures.update',
                'admin.textures.destroy',
                
                // Products
                'admin.products.index',
                'admin.products.trash',
                'admin.products.create',
                'admin.products.show',
                'admin.products.store',
                'admin.products.filter',
                'admin.products.edit',
                'admin.products.update',
                'admin.products.destroy',
                'admin.products.toggleFeature',
                'admin.products.restore',
                'admin.products.force-delete',
                'admin.products.images.store',
                'admin.variants.images.store',
                
                // Profile
                'admin.profile',
                'admin.profile.edit',
                'admin.profile.update',
                
                // Banners
                'admin.banners.index',
                'admin.banners.trash',
                'admin.banners.create',
                'admin.banners.show',
                'admin.banners.store',
                'admin.banners.updateOrder',
                'admin.banners.edit',
                'admin.banners.update',
                'admin.banners.destroy',
                'admin.banners.restore',
                'admin.banners.force-delete',
                
                // Posts
                'admin.posts.index',
                'admin.posts.trash',
                'admin.posts.create',
                'admin.posts.show',
                'admin.posts.store',
                'admin.posts.edit',
                'admin.posts.update',
                'admin.posts.destroy',
                'admin.posts.restore',
                'admin.posts.force-delete',
                
                // Reviews
                'admin.reviews.index',
                'admin.reviews.show',
                'admin.reviews.toggleStatus',
                'admin.reviews.destroy',
                
                // Users (chỉ một số quyền cơ bản)
                'admin.users.index',
                'admin.users.edit',
                'admin.users.update',
            ];
            
            $staffPermissionIds = Permission::whereIn('name', $staffPermissions)->pluck('id')->toArray();
            $staffRole->permissions()->sync($staffPermissionIds);
            
            $this->command->info("Đã gán " . count($staffPermissionIds) . " permissions cho role Staff (ID: {$staffRole->id})");
        } else {
            $this->command->warn('Không tìm thấy role Staff!');
        }
    }
}

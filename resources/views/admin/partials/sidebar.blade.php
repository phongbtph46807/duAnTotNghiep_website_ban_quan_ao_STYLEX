<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <div class="style-x-logo-sm">
                    <span class="style-text">S</span>
                    <span class="x-text">X</span>
                </div>
            </span>
            <span class="logo-lg">
                <div class="style-x-logo-lg">
                    <span class="style-text">Style</span>
                    <span class="x-text">X</span>
                </div>
            </span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <div class="style-x-logo-sm">
                    <span class="style-text">S</span>
                    <span class="x-text">X</span>
                </div>
            </span>
            <span class="logo-lg">
                <div class="style-x-logo-lg">
                    <span class="style-text">Style</span>
                    <span class="x-text">X</span>
                </div>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">

                <!-- ===== QUẢN LÝ SẢN PHẨM ===== -->
                <li class="menu-title"><span>Sản Phẩm</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="ri-shopping-bag-line"></i> <span>Quản lý sản phẩm</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}" class="nav-link">
                                    <i class="ri-list-check me-1"></i> Danh sách sản phẩm
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.create') }}" class="nav-link">
                                    <i class="ri-add-circle-line me-1"></i> Thêm sản phẩm mới
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.trash') }}" class="nav-link">
                                    <i class="ri-delete-bin-line me-1"></i> Sản phẩm đã xóa
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="ri-folder-2-line"></i> <span>Quản lí danh mục</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link">
                                    <i class="ri-list-check me-1"></i> Danh sách danh mục
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="openAddCategoryModal()">
                                    <i class="ri-add-circle-line me-1"></i> Thêm mới danh mục
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAttributes" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAttributes">
                        <i class="ri-palette-line"></i> <span>Thuộc tính sản phẩm</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAttributes">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.colors.index') }}" class="nav-link">
                                    <i class="ri-palette-line me-1"></i> Quản lý màu sắc
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.sizes.index') }}" class="nav-link">
                                    <i class="ri-ruler-line me-1"></i> Quản lý kích thước
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.textures.index') }}" class="nav-link">
                                    <i class="ri-scissors-line me-1"></i> Quản lý chất liệu
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- ===== QUẢN LÝ ĐƠN HÀNG ===== -->
                <li class="menu-title"><span>Đơn Hàng & Bán Hàng</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarOrders" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarOrders">
                        <i class="ri-shopping-bag-3-line"></i> <span>Quản lý đơn hàng</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarOrders">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.orders.index') }}" class="nav-link"
                                    data-key="t-orders-list">
                                    <i class="ri-file-list-3-line me-1"></i> Danh sách đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.orders.fulfillment.index') }}" class="nav-link" data-key="t-fulfillment">
                                    <i class="ri-truck-line me-1"></i> Đóng gói & Giao hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.withdraw-requests.index') }}" class="nav-link"
                                    data-key="t-withdraw-requests">
                                    <i class="ri-wallet-3-line me-1"></i> Yêu cầu rút tiền
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarVouchers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarVouchers">
                            <i class="ri-ticket-line"></i> <span>Quản lý voucher</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarVouchers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.vouchers.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách voucher
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.vouchers.create') }}" class="nav-link">
                                        <i class="ri-add-circle-line me-1"></i> Tạo voucher
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTaxShip" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTaxShip">
                            <i class="ri-truck-line"></i> <span>Thuế & Vận chuyển</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTaxShip">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.tax_rates.index') }}" class="nav-link">
                                        <i class="ri-bill-line me-1"></i> Thuế (Tax Rates)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.shipping_carriers.index') }}" class="nav-link">
                                        <i class="ri-truck-line me-1"></i> Đơn vị vận chuyển
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReviews" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReviews">
                            <i class="ri-star-line"></i> <span>Quản lý đánh giá</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReviews">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.reviews.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách đánh giá
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                <!-- ===== QUẢN LÝ KHO ===== -->
                <li class="menu-title"><span>Kho Hàng (WMS)</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarInventory" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInventory">
                        <i class="ri-store-3-line"></i> <span>Quản lý kho hàng</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarInventory">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.inventory.dashboard') }}" class="nav-link">
                                    <i class="ri-dashboard-line me-1"></i> Tổng quan kho
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#inventoryOperations" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="inventoryOperations">
                                    <i class="ri-exchange-box-line me-1"></i> <span>Giao dịch kho</span>
                                </a>
                                <div class="collapse" id="inventoryOperations">
                                    <ul class="nav nav-sm flex-column ms-3">
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.stock-in.index') }}" class="nav-link">
                                                <i class="ri-download-2-line me-1"></i> Nhập kho
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.transfer.index') }}" class="nav-link">
                                                <i class="ri-arrow-left-right-line me-1"></i> Chuyển kho
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#inventoryControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="inventoryControl">
                                    <i class="ri-checkbox-multiple-line me-1"></i> <span>Kiểm soát kho</span>
                                </a>
                                <div class="collapse" id="inventoryControl">
                                    <ul class="nav nav-sm flex-column ms-3">
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.count.index') }}" class="nav-link">
                                                <i class="ri-file-list-3-line me-1"></i> Kiểm kê
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.defect.index') }}" class="nav-link">
                                                <i class="ri-error-warning-line me-1"></i> Hàng hỏng
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.stock-out-invoice.index') }}" class="nav-link">
                                                <i class="ri-file-text-line me-1"></i> Hóa đơn thanh lý
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#inventoryReports" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="inventoryReports">
                                    <i class="ri-bar-chart-line me-1"></i> <span>Báo cáo & Thống kê</span>
                                </a>
                                <div class="collapse" id="inventoryReports">
                                    <ul class="nav nav-sm flex-column ms-3">
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.current-stock') }}" class="nav-link">
                                                <i class="ri-database-line me-1"></i> Tồn kho hiện tại
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.reports') }}" class="nav-link">
                                                <i class="ri-file-chart-line me-1"></i> Báo cáo tồn kho
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.inventory.logs') }}" class="nav-link">
                                                <i class="ri-history-line me-1"></i> Lịch sử giao dịch
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.inventory.warehouses.index') }}" class="nav-link">
                                    <i class="ri-home-4-line me-1"></i> Quản lý Kho hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.inventory.settings') }}" class="nav-link">
                                    <i class="ri-settings-3-line me-1"></i> Cài đặt kho
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.notifications.index') }}" class="nav-link">
                        <i class="ri-notification-badge-line me-1"></i> Thông báo
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        @if ($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- ===== GIAO DIỆN & TRUYỀN THÔNG ===== -->
                @if (auth()->user()->role != \App\Models\User::ROLE_WAREHOUSE_MANAGER)
                    <li class="menu-title"><span>Giao Diện & Truyền Thông</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarBanners" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarBanners">
                            <i class="ri-image-add-line"></i> <span>Quản lí banner</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarBanners">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.banners.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách banner
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.banners.create') }}" class="nav-link">
                                        <i class="ri-add-circle-line me-1"></i> Thêm mới banner
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.banners.trash') }}" class="nav-link">
                                        <i class="ri-delete-bin-line me-1"></i> Danh sách banner đã xóa
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPosts" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarPosts">
                            <i class="ri-pages-line"></i> <span>Quản lí bài viết</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPosts">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.posts.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách bài viết
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.posts.create') }}" class="nav-link">
                                        <i class="ri-add-circle-line me-1"></i> Thêm bài viết mới
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.posts.trash') }}" class="nav-link">
                                        <i class="ri-delete-bin-line me-1"></i> Danh sách bài viết đã xóa
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ===== QUẢN LÝ NGƯỜI DÙNG ===== -->
                <li class="menu-title"><span>Người Dùng & Quyền Hạn</span></li>

                @if (auth()->user()->role == 1 || auth()->user()->role == 2)
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
                            <i class="ri-account-circle-line"></i> <span>Quản lí người dùng</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarUsers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link">
                                        <i class="ri-user-line me-1"></i> Danh sách người dùng
                                    </a>
                                </li>
                                @if (auth()->user()->role == 1)
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.create') }}" class="nav-link">
                                            <i class="ri-user-add-line me-1"></i> Tạo User mới
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.trash') }}" class="nav-link">
                                            <i class="ri-delete-bin-line me-1"></i> Người dùng đã xóa
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (auth()->user()->role == 1)
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPermissions" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPermissions">
                            <i class="ri-shield-star-line"></i> <span>Quản Lý Quyền Hạn</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPermissions">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.roles.index') }}" class="nav-link">
                                        <i class="ri-user-settings-line me-1"></i> Phân quyền người dùng
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.rbac.roles.index') }}" class="nav-link">
                                        <i class="ri-shield-star-line me-1"></i> Quản lý Roles
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.rbac.permissions.index') }}" class="nav-link">
                                        <i class="ri-key-2-line me-1"></i> Quản lý Permissions
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarLoyalty" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLoyalty">
                            <i class="ri-vip-crown-line"></i> <span>Cấp độ thành viên</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarLoyalty">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.loyalty-tiers.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách cấp độ
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.loyalty-tiers.create') }}" class="nav-link">
                                        <i class="ri-add-circle-line me-1"></i> Thêm cấp độ mới
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- ===== QUẢN LÝ HỆ THỐNG ===== -->
                    <li class="menu-title"><span>Hệ Thống</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSalaries" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSalaries">
                            <i class="ri-money-dollar-circle-line"></i> <span>Quản lý trả lương</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSalaries">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.salaries.index') }}" class="nav-link">
                                        <i class="ri-list-check me-1"></i> Danh sách lương
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.salaries.create') }}" class="nav-link">
                                        <i class="ri-add-circle-line me-1"></i> Thêm lương thủ công
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.salaries.generate-by-role') }}" class="nav-link">
                                        <i class="ri-automatic-line me-1"></i> Tạo lương tự động
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.salaries.history') }}" class="nav-link">
                                        <i class="ri-history-line me-1"></i> Lịch sử lương
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<link rel="stylesheet" href="{{ asset('assets/css/style-x-logo.css') }}">
<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>

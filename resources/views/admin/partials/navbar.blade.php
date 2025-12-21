<div class="navbar-header">
    <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box horizontal-logo">
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
        </div>

        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
            id="topnav-hamburger-icon">
            <span class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <!-- App Search-->
        <form class="app-search d-none d-md-block">
            <div class="position-relative">
                <input type="text" class="form-control" placeholder="Search..." autocomplete="off"
                    id="search-options" value="">
                <span class="mdi mdi-magnify search-widget-icon"></span>
                <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                    id="search-close-options"></span>
            </div>
        </form>
    </div>

    <div class="d-flex align-items-center">
        <div class="dropdown d-md-none topbar-head-dropdown header-item">
            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="bx bx-search fs-22"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                aria-labelledby="page-header-search-dropdown">
                <form class="p-3">
                    <div class="form-group m-0">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search ..."
                                aria-label="Recipient's username">
                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="ms-1 header-item d-none d-sm-flex">
            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                data-toggle="fullscreen">
                <i class='bx bx-fullscreen fs-22'></i>
            </button>
        </div>

        <div class="ms-1 header-item d-none d-sm-flex">
            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                <i class='bx bx-moon fs-22'></i>
            </button>
        </div>

        {{-- Icon thông báo đơn hàng và yêu cầu --}}
        <div class="dropdown ms-1 header-item d-none d-sm-flex">
            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle position-relative" id="orderNotificationsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Thông báo đơn hàng">
                <i class='ri-notification-3-line fs-22'></i>
                @php
                    $pendingRequestsCount = \App\Models\Order::whereIn('status', ['cancel_request', 'return_request'])->count();
                    $newOrdersCount = \App\Models\Order::where('status', 'pending')
                        ->where('created_at', '>=', now()->subDay())
                        ->count();
                    $totalNotifications = $pendingRequestsCount + $newOrdersCount;
                @endphp
                @if ($totalNotifications > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="navbarPendingRequestsBadge" style="font-size: 10px; padding: 2px 5px;">
                        {{ $totalNotifications }}
                    </span>
                @else
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="navbarPendingRequestsBadge" style="font-size: 10px; padding: 2px 5px;">0</span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="orderNotificationsDropdown" style="width: 380px; max-height: 500px; overflow-y: auto;">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0">Thông báo đơn hàng</h6>
                </div>
                <div id="notificationsContent">
                    <div class="text-center p-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
                <div class="p-2 border-top text-center">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link text-decoration-none">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>

        <div class="dropdown ms-sm-3 header-item topbar-user">
            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="d-flex align-items-center">

                    <img class="rounded-circle header-profile-user" src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT }}"
                        alt="Header Avatar">
                    <span class="text-start ms-xl-2">
                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                            {{ Auth::user()->name ?? null}}
                            @php
                                $user = Auth::user();
                                $userRoles = $user->roles;
                                $displayRole = null;
                                
                                // Ưu tiên lấy role từ RBAC
                                if ($userRoles && $userRoles->isNotEmpty()) {
                                    $displayRole = $userRoles->first();
                                } else {
                                    // Fallback về role integer
                                    $roleName = match($user->role) {
                                        1 => 'Admin',
                                        2 => 'Staff',
                                        3 => 'Warehouse Manager',
                                        default => null
                                    };
                                    if ($roleName) {
                                        $displayRole = \App\Models\Role::where('name', $roleName)->first();
                                    }
                                }
                            @endphp
                            @if($displayRole)
                                <span class="badge bg-{{ $displayRole->color ?? 'secondary' }}-subtle text-{{ $displayRole->color ?? 'secondary' }} ms-1">{{ $displayRole->name }}</span>
                            @elseif($user->role == 0)
                                <span class="badge bg-secondary-subtle text-secondary ms-1">User</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary ms-1">Unknown</span>
                            @endif
                        </span>
                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ Auth::user()->email ?? null }}</span>
                    </span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->

                <h6 class="dropdown-header">
                    @php
                        $user = Auth::user();
                        $userRoles = $user->roles;
                        $greetingRole = null;
                        
                        // Ưu tiên lấy role từ RBAC
                        if ($userRoles && $userRoles->isNotEmpty()) {
                            $greetingRole = $userRoles->first();
                        } else {
                            // Fallback về role integer
                            $roleName = match($user->role) {
                                1 => 'Admin',
                                2 => 'Staff',
                                3 => 'Warehouse Manager',
                                default => null
                            };
                            if ($roleName) {
                                $greetingRole = \App\Models\Role::where('name', $roleName)->first();
                            }
                        }
                    @endphp
                    @if($greetingRole)
                        Xin chào {{ $greetingRole->name }} {{ Auth::user()->name ?? null }}! 
                    @else
                        Xin chào {{ Auth::user()->name ?? null }}! 
                    @endif
                </h6>
                <a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                        class="align-middle">Thông tin cá nhân</span></a>    
                        <a class="dropdown-item" href="#">
                        <i
                        class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                        class="align-middle">Cài đặt</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">
                        <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('assets/css/style-x-logo.css') }}">

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TextureController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleEntityController;
use App\Http\Controllers\Admin\PermissionEntityController;
use App\Http\Controllers\Admin\LoyaltyTierController;
use App\Http\Controllers\Admin\TaxRateController;
use App\Http\Controllers\Admin\ShippingCarrierController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CountController;
use App\Http\Controllers\Admin\DefectAssessmentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderFulfillmentController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\StockOutInvoiceController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\SalaryController;

// Admin và Staff routes - cả hai đều có thể truy cập
Route::group(['middleware' => ['onlyAuthenticated', 'checkRole:1,2']], function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //Categories route
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/admin-category-create', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

        // Colors, Sizes, Textures routes
        Route::resource('colors', ColorController::class);
        Route::resource('sizes', SizeController::class);
        Route::resource('textures', TextureController::class);

        // Products routes
        Route::prefix('products')->as('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/trash', [ProductController::class, 'trash'])->name('trash');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::post('/filter', [ProductController::class, 'filter'])->name('filter');
            Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{product}/toggle-feature', [ProductController::class, 'toggleFeature'])->name('toggleFeature');
            Route::patch('/{id}/restore', [ProductController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('force-delete');

            Route::post('/{product}/images', [ProductImageController::class, 'storeProduct'])->name('images.store');
        });
        Route::post('/variants/{variant}/images', [ProductImageController::class, 'storeVariant'])->name('variants.images.store');

        // Profile routes
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

        // Banners routes
        Route::prefix('banners')->as('banners.')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/trash', [BannerController::class, 'trash'])->name('trash');
            Route::get('/create', [BannerController::class, 'create'])->name('create');
            Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
            Route::post('/store', [BannerController::class, 'store'])->name('store');
            Route::post('/update-order', [BannerController::class, 'updateOrder'])->name('updateOrder');
            Route::get('/edit/{banner}', [BannerController::class, 'edit'])->name('edit');
            Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
            Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [BannerController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('force-delete');
        });

        // Post routes
        Route::prefix('posts')->as('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/trash', [PostController::class, 'trash'])->name('trash');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::get('/{id}', [PostController::class, 'show'])->name('show');
            Route::post('/store', [PostController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PostController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [PostController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [PostController::class, 'forceDelete'])->name('force-delete');
        });

        // Reviews routes
        Route::prefix('reviews')->as('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
            Route::patch('/{id}/toggle-status', [ReviewController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
        });
        // Orders list: allow both Admin and Staff to view orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        
        // Orders management - cho phép Staff cập nhật trạng thái và duyệt hủy/trả hàng
        Route::prefix('orders')->as('orders.')->group(function () {
            Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{order}/approve-cancel', [OrderController::class, 'approveCancel'])->name('approveCancel');
            Route::post('/{order}/approve-return', [OrderController::class, 'approveReturn'])->name('approveReturn');
        });
    });
});

// Users management - CHUNG cho cả ADMIN và STAFF
Route::group(['middleware' => ['onlyAuthenticated', 'checkRole:1,2']], function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::prefix('users')->as('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
        });
    });
});

// Shared inventory routes cho Admin và Warehouse Manager
Route::group(['middleware' => ['onlyAuthenticated', 'checkRole:1,3']], function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        Route::prefix('inventory')->as('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
            Route::get('logs', [InventoryController::class, 'showLogs'])->name('logs');
            Route::get('current-stock', [InventoryController::class, 'currentStock'])->name('current-stock');
            Route::get('reports', [InventoryController::class, 'reports'])->name('reports');
            Route::get('settings', [InventoryController::class, 'settings'])->name('settings');
            Route::post('settings', [InventoryController::class, 'updateSettings'])->name('settings.update');

            Route::prefix('stock-in')->as('stock-in.')->group(function () {
                Route::get('/', [StockInController::class, 'index'])->name('index');
                Route::get('create', [StockInController::class, 'create'])->name('create');
                Route::post('/', [StockInController::class, 'store'])->name('store');
                Route::get('{id}/qc', [StockInController::class, 'qc'])->name('qc');
                Route::post('{id}/confirm-qc', [StockInController::class, 'confirmQC'])->name('confirm-qc');
                Route::post('{id}/confirm', [StockInController::class, 'confirm'])->name('confirm');
                Route::post('{id}/reject', [StockInController::class, 'reject'])->name('reject');
            });

            Route::prefix('stock-out')->as('stock-out.')->group(function () {
                Route::get('/', [StockOutController::class, 'index'])->name('index');
                Route::get('create', [StockOutController::class, 'create'])->name('create');
                Route::post('/', [StockOutController::class, 'store'])->name('store');
                Route::get('{id}/qc', [StockOutController::class, 'qc'])->name('qc');
                Route::post('{id}/confirm-qc', [StockOutController::class, 'confirmQC'])->name('confirm-qc');
                Route::post('{id}/confirm', [StockOutController::class, 'confirm'])->name('confirm');
                Route::post('{id}/reject', [StockOutController::class, 'reject'])->name('reject');
            });

            Route::prefix('transfer')->as('transfer.')->group(function () {
                Route::get('/', [TransferController::class, 'index'])->name('index');
                Route::get('create', [TransferController::class, 'create'])->name('create');
                Route::post('/', [TransferController::class, 'store'])->name('store');
                Route::post('{id}/confirm-out', [TransferController::class, 'confirmOut'])->name('confirm-out');
                Route::post('{id}/confirm-in', [TransferController::class, 'confirmIn'])->name('confirm-in');
            });

            Route::prefix('count')->as('count.')->group(function () {
                Route::get('/', [CountController::class, 'index'])->name('index');
                Route::get('create', [CountController::class, 'create'])->name('create');
                Route::post('/', [CountController::class, 'store'])->name('store');
                Route::get('{id}/count', [CountController::class, 'count'])->name('count');
                Route::post('{id}/confirm-count', [CountController::class, 'confirmCount'])->name('confirm-count');
                Route::post('{id}/confirm-adjustment', [CountController::class, 'confirmAdjustment'])->name('confirm-adjustment');
            });

            Route::prefix('defect')->as('defect.')->group(function () {
                Route::get('/', [DefectAssessmentController::class, 'index'])->name('index');
                Route::get('create', [DefectAssessmentController::class, 'create'])->name('create');
                Route::post('/', [DefectAssessmentController::class, 'store'])->name('store');
                Route::get('{id}/assess', [DefectAssessmentController::class, 'assess'])->name('assess');
                Route::post('{id}/confirm-assess', [DefectAssessmentController::class, 'confirmAssess'])->name('confirm-assess');
                Route::post('{id}/approve', [DefectAssessmentController::class, 'approve'])->name('approve');
                Route::post('{id}/complete', [DefectAssessmentController::class, 'complete'])->name('complete');
                Route::post('{id}/reject', [DefectAssessmentController::class, 'reject'])->name('reject');
            });

            Route::prefix('stock-out-invoice')->as('stock-out-invoice.')->group(function () {
                Route::get('/', [StockOutInvoiceController::class, 'index'])->name('index');
                Route::get('{id}', [StockOutInvoiceController::class, 'show'])->name('show')->where('id', '[0-9]+');
                Route::post('{id}/complete', [StockOutInvoiceController::class, 'complete'])->name('complete')->where('id', '[0-9]+');
            });

            Route::prefix('warehouses')->as('warehouses.')->group(function () {
                Route::get('/', [WarehouseController::class, 'index'])->name('index');
                Route::get('create', [WarehouseController::class, 'create'])->name('create');
                Route::post('/', [WarehouseController::class, 'store'])->name('store');
                Route::get('{warehouse}', [WarehouseController::class, 'show'])->name('show');
                Route::get('{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
                Route::put('{warehouse}', [WarehouseController::class, 'update'])->name('update');
                Route::delete('{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('notifications')->as('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        });
    });
});

// Routes chỉ dành cho Admin (role=1)
Route::group(['middleware' => ['onlyAuthenticated', 'checkRole:1']], function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        // Role Management
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{user}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{user}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{user}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/check-admin-count', [RoleController::class, 'checkAdminCount'])->name('roles.check-admin-count');
        Route::post('/roles/{user}/update-role', [RoleController::class, 'updateRole'])->name('roles.update-role');
        Route::post('/roles/bulk-update', [RoleController::class, 'bulkUpdateRoles'])->name('roles.bulk-update');

        // Loyalty Tiers
        Route::resource('loyalty-tiers', LoyaltyTierController::class)->parameters(['loyalty-tiers' => 'loyaltyTier']);

        // Tax & Shipping
        Route::resource('tax_rates', TaxRateController::class);
        Route::resource('shipping_carriers', ShippingCarrierController::class);

        // Vouchers
        Route::resource('vouchers', VoucherController::class);

        // RBAC
        Route::prefix('rbac')->as('rbac.')->group(function () {
            Route::resource('roles', RoleEntityController::class)->except(['show']);
            Route::resource('permissions', PermissionEntityController::class)->except(['show']);
        });

        // Orders - các route này chỉ dành cho Admin
        Route::prefix('orders')->as('orders.')->group(function () {
            // Route index đã được định nghĩa ở trên cho cả Admin và Staff, không cần định nghĩa lại
            Route::get('{order}', [OrderController::class, 'show'])->name('show');
            Route::post('{order}/confirm', [OrderController::class, 'confirm'])->name('confirm');
            Route::post('{order}/ship', [OrderController::class, 'ship'])->name('ship');
            Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            // Route updateStatus đã được định nghĩa ở trên cho cả Admin và Staff, không cần định nghĩa lại
            Route::post('{itemId}/return', [OrderController::class, 'returnItem'])->name('returnItem');

            Route::prefix('fulfillment')->as('fulfillment.')->group(function () {
                Route::get('/', [OrderFulfillmentController::class, 'index'])->name('index');
                Route::post('{order}/confirm', [OrderFulfillmentController::class, 'confirm'])->name('confirm');
                Route::get('{order}/picking', [OrderFulfillmentController::class, 'startPicking'])->name('picking');
                Route::post('{order}/picking', [OrderFulfillmentController::class, 'storePicking'])->name('picking.store');
                Route::post('{picking}/pack', [OrderFulfillmentController::class, 'completePacking'])->name('pack');
                Route::post('{order}/ship', [OrderFulfillmentController::class, 'ship'])->name('ship');
            });
        });

        // Users
        
        // Orders management - CHỈ ADMIN
        // Các route updateStatus, approveCancel, approveReturn đã được định nghĩa cho cả Admin và Staff ở trên
        // Chỉ còn payment-status là chỉ dành cho Admin
        Route::post('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
        
        //Route Users - CHỈ ADMIN (bổ sung thêm chức năng)
        Route::prefix('users')->as('users.')->group(function () {
            Route::get('/trash', [UserController::class, 'trash'])->name('trash');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::post('/filter', [UserController::class, 'filter'])->name('filter');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::put('/updateEmailVerified/{user}', [UserController::class, 'updateEmailVerified'])->name('updateEmailVerified');
            Route::patch('/{id}/restore', [UserController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        });

        // Salary - CHỈ ADMIN
        Route::prefix('salaries')->as('salaries.')->group(function () {
            Route::get('/', [SalaryController::class, 'index'])->name('index');
            Route::get('create', [SalaryController::class, 'create'])->name('create');
            Route::post('/', [SalaryController::class, 'store'])->name('store');
            Route::get('{salary}/edit', [SalaryController::class, 'edit'])->name('edit');
            Route::put('{salary}', [SalaryController::class, 'update'])->name('update');
            Route::delete('{salary}', [SalaryController::class, 'destroy'])->name('destroy');
            Route::get('generate-by-role', [SalaryController::class, 'generateByRole'])->name('generate-by-role');
            Route::post('generate-by-role', [SalaryController::class, 'storeGenerateByRole'])->name('store-generate-by-role');
            Route::get('history', [SalaryController::class, 'history'])->name('history');
        });

        // Salary approve/reject routes - separate to avoid conflicts
        Route::post('salaries/{id}/approve', [SalaryController::class, 'approve'])->name('salaries.approve');
        Route::post('salaries/{id}/reject', [SalaryController::class, 'reject'])->name('salaries.reject');

        // Role Salaries - CHỈ ADMIN
        Route::prefix('role-salaries')->as('role-salaries.')->group(function () {
            Route::get('/', [SalaryController::class, 'roleSalariesIndex'])->name('index');
            Route::get('create', [SalaryController::class, 'roleSalariesCreate'])->name('create');
            Route::post('/', [SalaryController::class, 'roleSalariesStore'])->name('store');
            Route::get('{id}/edit', [SalaryController::class, 'roleSalariesEdit'])->name('edit');
            Route::put('{id}', [SalaryController::class, 'roleSalariesUpdate'])->name('update');
            Route::delete('{id}', [SalaryController::class, 'roleSalariesDestroy'])->name('destroy');
        });
    });
});

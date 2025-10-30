<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TextureController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Client\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoyaltyTierController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleEntityController;
use App\Http\Controllers\Admin\PermissionEntityController;
use App\Http\Controllers\Admin\TaxRateController;
use App\Http\Controllers\Admin\ShippingCarrierController;
use App\Http\Controllers\Client\CheckoutController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Client Product routes
Route::prefix('products')->as('client.products.')->group(function () {
    Route::get('/', [ClientProductController::class, 'index'])->name('index');
    Route::get('/{id}', [ClientProductController::class, 'show'])->name('show');
});

// Client Cart routes
Route::prefix('cart')->as('client.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::get('/get', [CartController::class, 'getCart'])->name('get');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::prefix('checkout')->as('client.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/place', [CheckoutController::class, 'place'])->name('place');
});

Route::get('/checkout/thankyou/{id}', [CheckoutController::class, 'thankyou'])->name('client.checkout.thankyou');
Route::get('/order/track', [CheckoutController::class, 'track'])->name('client.order.track');
Route::get('/order/history', [CheckoutController::class, 'orderList'])->name('client.order.list');

Route::group(['middleware' => ['isAuthenticated']], function () {

    Route::get('/register', [AuthController::class, 'registerView'])->name('registerView');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

    Route::get('/login', [AuthController::class, 'loginView'])->name('loginView');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Logout route - cần middleware auth để đảm bảo user đã đăng nhập
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::group(['middleware' => ['onlyAuthenticated']], function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('user.dashboard');
});

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
        });

        // Profile routes - Admin và Staff đều có thể chỉnh sửa thông tin cá nhân
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

        // Post routes
        Route::prefix('post')->as('post.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/store', [PostController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PostController::class, 'update'])->name('update');
            Route::delete('/{id}', [PostController::class, 'destroy'])->name('destroy');
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

// Routes chỉ dành cho Admin (role=1) - Bổ sung thêm chức năng
Route::group(['middleware' => ['onlyAuthenticated', 'checkRole:1']], function () {
    Route::prefix('admin')->as('admin.')->group(function () {
        // Role Management - CHỈ ADMIN
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{user}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{user}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{user}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/check-admin-count', [RoleController::class, 'checkAdminCount'])->name('roles.check-admin-count');
        Route::post('/roles/{user}/update-role', [RoleController::class, 'updateRole'])->name('roles.update-role');
        Route::post('/roles/bulk-update', [RoleController::class, 'bulkUpdateRoles'])->name('roles.bulk-update');

        // Loyalty Tiers - CHỈ ADMIN
        Route::resource('loyalty-tiers', LoyaltyTierController::class)
            ->parameters(['loyalty-tiers' => 'loyaltyTier']);

        // Tax & Shipping routes - CHỈ ADMIN
        Route::resource('tax_rates', TaxRateController::class);
        Route::resource('shipping_carriers', ShippingCarrierController::class);

        // RBAC Entities (roles & permissions) - CHỈ ADMIN, entity management only
        Route::prefix('rbac')->as('rbac.')->group(function () {
            Route::resource('roles', RoleEntityController::class)->except(['show']);
            Route::resource('permissions', PermissionEntityController::class)->except(['show']);
        });

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
        //order management
        
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    });
});

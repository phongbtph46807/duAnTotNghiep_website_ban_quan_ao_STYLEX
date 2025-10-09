<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoyaltyTierController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Routes chỉ dành cho người dùng chưa xác thực
Route::group(['middleware' => ['isAuthenticated']], function() {

    Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
    Route::post('/register', [AuthController::class,'register'])->name('register');

    Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

    Route::get('/login', [AuthController::class,'loginView'])->name('loginView');
    Route::post('/login', [AuthController::class,'login'])->name('login');
});

// Routes chỉ dành cho người dùng đã xác thực
Route::group(['middleware' => ['onlyAuthenticated']], function() {

    Route::get('/dashboard', function(){
        return 'User Dashboard';
    })->name('user.dashboard');

});

// Admin routes
Route::group(['middleware' => ['onlyAuthenticated','onlyAdmin']], function() {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    //Categories route
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin-category-create',[CategoryController::class, 'store'])->name('admin.category.store');
});

// Loyalty Tiers
Route::prefix('admin/loyalty-tiers')->name('admin.loyalty-tiers.')->group(function () {
    Route::get('/', [LoyaltyTierController::class, 'index'])->name('index');
    Route::get('/create', [LoyaltyTierController::class, 'create'])->name('create');
    Route::post('/', [LoyaltyTierController::class, 'store'])->name('store');
    Route::get('/{loyaltyTier}/edit', [LoyaltyTierController::class, 'edit'])->name('edit');
    Route::put('/{loyaltyTier}', [LoyaltyTierController::class, 'update'])->name('update');
    Route::delete('/{loyaltyTier}', [LoyaltyTierController::class, 'destroy'])->name('destroy');
});

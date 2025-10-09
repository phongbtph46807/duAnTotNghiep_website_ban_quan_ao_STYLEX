<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaxRateController;
use App\Http\Controllers\Admin\ShippingCarrierController;


Route::get('/', function () {
    return view('welcome');
});

// Route::group(['middleware' => ['isAuthenticated']], function() {

    Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
    Route::post('/register', [AuthController::class,'register'])->name('register');

    Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

    Route::get('/login', [AuthController::class,'loginView'])->name('loginView');
    Route::post('/login', [AuthController::class,'login'])->name('login');
// });

// Route::group(['middleware' => ['onlyAuthenticated']], function() {

    Route::get('/dashboard', function(){
        return 'User Dashboard';
    })->name('user.dashboard');

// });

// Route::group(['middleware' => ['onlyAuthenticated','onlyAdmin']], function() {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    //Categories route
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin-category-create',[CategoryController::class, 'store'])->name('admin.category.store');
// });

// Tax Rates routes
Route::get('/admin/tax-rates', [TaxRateController::class, 'index'])->name('admin.tax_rates.index');
Route::get('/admin/tax-rates/create', [TaxRateController::class, 'create'])->name('admin.tax_rates.create');
Route::post('/admin/tax-rates', [TaxRateController::class, 'store'])->name('admin.tax_rates.store');
Route::get('/admin/tax-rates/{tax_rate}/edit', [TaxRateController::class, 'edit'])->name('admin.tax_rates.edit');
Route::put('/admin/tax-rates/{tax_rate}', [TaxRateController::class, 'update'])->name('admin.tax_rates.update');
Route::delete('/admin/tax-rates/{tax_rate}', [TaxRateController::class, 'destroy'])->name('admin.tax_rates.destroy');

// Shipping Carriers routes
Route::get('/admin/shipping-carriers', [ShippingCarrierController::class, 'index'])->name('admin.shipping_carriers.index');
Route::get('/admin/shipping-carriers/create', [ShippingCarrierController::class, 'create'])->name('admin.shipping_carriers.create');
Route::post('/admin/shipping-carriers', [ShippingCarrierController::class, 'store'])->name('admin.shipping_carriers.store');
Route::get('/admin/shipping-carriers/{shipping_carrier}/edit', [ShippingCarrierController::class, 'edit'])->name('admin.shipping_carriers.edit');
Route::put('/admin/shipping-carriers/{shipping_carrier}', [ShippingCarrierController::class, 'update'])->name('admin.shipping_carriers.update');
Route::delete('/admin/shipping-carriers/{shipping_carrier}', [ShippingCarrierController::class, 'destroy'])->name('admin.shipping_carriers.destroy');

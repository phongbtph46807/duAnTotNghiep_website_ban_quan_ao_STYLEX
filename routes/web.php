<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['isAuthenticated']], function() {

    Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
    Route::post('/register', [AuthController::class,'register'])->name('register');

    Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

    Route::get('/login', [AuthController::class,'loginView'])->name('loginView');
    Route::post('/login', [AuthController::class,'login'])->name('login');
});

Route::group(['middleware' => ['onlyAuthenticated']], function() {

    Route::get('/dashboard', function(){
        return 'User Dashboard';
    })->name('user.dashboard');

});

Route::group(['middleware' => ['onlyAuthenticated','onlyAdmin']], function() {
    Route::prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Categories route
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/admin-category-create',[CategoryController::class, 'store'])->name('category.store');
    //Route Users   
    Route::prefix('users')->as('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::post('/filter', [UserController::class, 'filter'])->name('filter');
        Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/updateEmailVerified/{user}', [UserController::class, 'updateEmailVerified'])->name('updateEmailVerified');
        Route::patch('/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
    });
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
});
});

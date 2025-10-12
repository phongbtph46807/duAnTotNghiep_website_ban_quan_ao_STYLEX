<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TextureController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Test route để kiểm tra
Route::get('/test-categories', function () {
    $categories = App\Models\Category::with('children')->whereNull('parent_id')->get();
    return response()->json([
        'count' => $categories->count(),
        'categories' => $categories
    ]);
});

Route::group(['middleware' => ['isAuthenticated']], function() {

    Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
    Route::post('/register', [AuthController::class,'register'])->name('register');

    Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

    Route::get('/login', [AuthController::class,'loginView'])->name('loginView');
    Route::post('/login', [AuthController::class,'login'])->name('login');
});

// Logout route - cần middleware auth để đảm bảo user đã đăng nhập
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth')->name('logout');

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
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    
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
});
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('colors', ColorController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('textures', TextureController::class);
});
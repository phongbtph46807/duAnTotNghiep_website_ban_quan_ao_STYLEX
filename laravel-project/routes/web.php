<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
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

    Route::get('/admin/dashboard', [AppController::class, 'index'])->name('admin.dashboard');
});

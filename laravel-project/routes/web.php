<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
Route::post('/register', [AuthController::class,'register'])->name('register');

Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');

Route::get('/login', [AuthController::class,'loginView'])->name('loginView');
Route::post('/login', [AuthController::class,'login'])->name('login');

Route::get('/dashboard', function(){
    return 'User Dashboard';
})->name('user.dashboard');

Route::get('/admin/dashboard', function(){
    return 'Admin Dashboard';
})->name('admin.dashboard');
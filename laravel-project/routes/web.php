<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/admin', function () {
    return view('admin.home-admin');
});

Route::get('/register', [AuthController::class,'registerView'])->name('registerView');
Route::post('/register', [AuthController::class,'register'])->name('register');

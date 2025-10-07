<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TextureController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/admin', function () {
    return view('admin.home-admin');
});



Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('colors', ColorController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('textures', TextureController::class);
});




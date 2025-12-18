<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProvinceController;

Route::get('/products/filter', [ProductController::class, 'index'])->name('api.products.filter');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

// Review routes
Route::post('/reviews', [ReviewController::class, 'store'])->name('api.reviews.store');
Route::get('/products/{productId}/reviews', [ReviewController::class, 'getProductReviews'])->name('api.reviews.product');

// Province routes (DS cac tinh thanh)
Route::get('/provinces', [ProvinceController::class, 'getProvinces'])->name('api.provinces');
Route::get('/communes', [ProvinceController::class, 'getCommunes'])->name('api.communes');
Route::post('/address-convert', [ProvinceController::class, 'convertAddress'])->name('api.address.convert');

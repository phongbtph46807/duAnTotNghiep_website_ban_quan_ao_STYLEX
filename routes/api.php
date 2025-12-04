<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/products/filter', [ProductController::class, 'index'])->name('api.products.filter');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

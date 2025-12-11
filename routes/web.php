<?php

require __DIR__ . '/client.php';
require __DIR__ . '/admin.php';

// Test routes
Route::get('/test-low-stock', [\App\Http\Controllers\TestLowStockController::class, 'test']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoyaltyTierController;


Route::get('/', function () {
    return view('auth.login');
});


// Nhóm Route cho khu vực Admin
Route::prefix('admin')
    // ->middleware(['auth', 'can:access-admin-panel'])
    ->group(function () {

        // Định nghĩa Route Resource cho LoyaltyTiers
        Route::resource('loyalty-tiers', LoyaltyTierController::class);
    });

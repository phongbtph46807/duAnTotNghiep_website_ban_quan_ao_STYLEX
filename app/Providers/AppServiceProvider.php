<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;    
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // Share cart count to all views
        View::composer('*', function ($view) {
            $cart = Session::get('cart', []);
            $cartCount = 0;
            
            foreach ($cart as $item) {
                $cartCount += $item['quantity'];
            }
            
            $view->with('cartCount', $cartCount);
        });
    }
}

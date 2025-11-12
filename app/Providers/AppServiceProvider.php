<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        View::composer('*', function ($view) {

            // Dùng cache (lưu 1 giờ) 
            $menuCategories = cache()->remember('menu_categories', 3600, function () {
                return Category::whereNull('parent_id') // 1. Chỉ lấy danh mục cha (cấp 1)
                    ->where('status', 1)                 // 2. Chỉ lấy các danh mục đang hoạt động
                    ->with('childrenRecursive')         // 3. Lấy TẤT CẢ con/cháu đệ quy
                    ->orderBy('id', 'asc')          // 4. Sắp xếp theo id
                    ->get();
            });
            // Chia sẻ biến $menuCategories cho tất cả các file blade
            $view->with('menuCategories', $menuCategories);
        });
    }
}

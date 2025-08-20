<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('partial.header', function ($view) {
            $categories_header = Category::whereNull('parent_id')
                                  ->with('children')
                                  ->get();

            $view->with('categories_header', $categories_header);
        });
    }
}

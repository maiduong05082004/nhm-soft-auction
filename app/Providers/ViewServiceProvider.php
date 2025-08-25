<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Services\Config\ConfigService;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $configService = app(ConfigService::class);
            $marquee = $configService->getConfigValue('MARQUEE_CONTENT');

            $view->with('marquee', $marquee);
        });
        View::composer('partial.header', function ($view) {
            $categories_header = Category::whereNull('parent_id')
                ->with('children')
                ->get();

            $view->with('categories_header', $categories_header);
        });
    }
}

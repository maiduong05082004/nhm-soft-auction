<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Category;
use App\Services\Articles\ArticleService;
use App\Services\Articles\ArticleServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\BaseService;
use App\Services\BaseServiceInterface;
use App\Services\Cart\CartService;
use App\Services\Cart\CartServiceInterface;
use App\Services\Category\CategoryService;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Order\OrderService;
use App\Services\Order\OrderServiceInterface;
use App\Services\Orders\OrderDetailService;
use App\Services\Orders\OrderDetailServiceInterface;
use App\Services\Products\ProductService;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceAppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseServiceInterface::class, BaseService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(OrderDetailServiceInterface::class, OrderDetailService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

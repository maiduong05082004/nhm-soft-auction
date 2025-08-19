<?php

namespace App\Providers;

use App\Services\Articles\ArticleService;
use App\Services\Articles\ArticleServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\BaseService;
use App\Services\BaseServiceInterface;
use App\Services\Cart\CartService;
use App\Services\Cart\CartServiceInterface;
use App\Services\Config\ConfigService;
use App\Services\Config\ConfigServiceInterface;
use App\Services\Category\CategoryService;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Orders\OrderService;
use App\Services\Orders\OrderServiceInterface;
use App\Services\Transaction\TransactionService;
use App\Services\Transaction\TransactionServiceInterface;
use App\Services\Products\ProductService;
use App\Services\Products\ProductServiceInterface;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\CheckoutServiceInterface;
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
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        $this->app->bind(ConfigServiceInterface::class, ConfigService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(CheckoutServiceInterface::class, CheckoutService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(OrderRepository::class, function ($app) {
            return new OrderRepository(new Order());
        });
        
        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository(new Product());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

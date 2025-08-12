<?php

namespace App\Providers;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\BaseService;
use App\Services\BaseServiceInterface;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class ServiceAppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseServiceInterface::class, BaseService::class);
        $this->app->bind(OrderService::class, function ($app) {
            $orderRepo = $app->make(OrderRepository::class);
            $productRepo = $app->make(ProductRepository::class);
            return new OrderService($orderRepo, $productRepo);
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

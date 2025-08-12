<?php

namespace App\Providers;

use App\Services\BaseService;
use App\Services\BaseServiceInterface;
use App\Services\Orders\OrderServiceInterface;
use App\Services\Orders\OrderService;
use Illuminate\Support\ServiceProvider;

class ServiceAppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseServiceInterface::class, BaseService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

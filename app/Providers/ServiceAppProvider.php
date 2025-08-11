<?php

namespace App\Providers;

use App\Services\BaseService;
use App\Services\BaseServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceAppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseServiceInterface::class, BaseService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Services\Cart\CartServiceInterface;
use App\Services\Wishlist\WishlistServiceInterface;
use App\Services\Config\ConfigService;
use App\Services\PageStatic\PageStaticServiceInterface;

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

            $headerCartCount = 0;
            $headerWishlistCount = 0;
            if (auth()->check()) {
                try {
                    $cartService = app(CartServiceInterface::class);
                    $cartSummary = $cartService->getCartSummary(auth()->id());
                    if (!empty($cartSummary['success']) && !empty($cartSummary['data'])) {
                        $headerCartCount = (int) ($cartSummary['data']['count'] ?? 0);
                    }
                } catch (\Throwable $e) {
                    $headerCartCount = 0;
                }

                try {
                    $wishlistService = app(WishlistServiceInterface::class);
                    $wishSummary = $wishlistService->getSummary(auth()->id());
                    if (!empty($wishSummary['success']) && !empty($wishSummary['data'])) {
                        $headerWishlistCount = (int) ($wishSummary['data']['count'] ?? 0);
                    }
                } catch (\Throwable $e) {
                    $headerWishlistCount = 0;
                }
            }

            $view->with(compact('categories_header', 'headerCartCount', 'headerWishlistCount'));
        });
        View::composer('partial.footer', function ($view) { 
            $pages = app(PageStaticServiceInterface::class)->getAll('pageStatic');
            $view->with(compact('pages'));
        });
    }
}

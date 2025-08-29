<?php

namespace App\Providers;

use App\Services\Articles\ArticleService;
use App\Services\Articles\ArticleServiceInterface;
use App\Services\Auctions\AuctionService;
use App\Services\Auctions\AuctionServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Banners\BannerService as BannersBannerService;
use App\Services\Banners\BannerServiceInterface;
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
use App\Services\Membership\MembershipService;
use App\Services\Membership\MembershipServiceInterface;
use App\Services\Transaction\TransactionService;
use App\Services\Transaction\TransactionServiceInterface;
use App\Services\Products\ProductService;
use App\Services\Products\ProductServiceInterface;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\CheckoutServiceInterface;
use App\Services\Wishlist\WishlistService;
use App\Services\Wishlist\WishlistServiceInterface;
use App\Services\Evaluates\EvaluateService;
use App\Services\Evaluates\EvaluateServiceInterface;
use App\Services\OrderDetails\OrderDetailService;
use App\Services\OrderDetails\OrderDetailServiceInterface;
use App\Services\PageStatic\PageStaticService;
use App\Services\PageStatic\PageStaticServiceInterface;
use App\Services\Payments\PaymentService;
use App\Services\Payments\PaymentServiceInterface;
use App\Services\PointPackages\PointPackageService;
use App\Services\PointPackages\PointPackageServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Users\UserRepository;
use App\Services\Checkout\MembershipDiscountService;

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
        $this->app->bind(WishlistServiceInterface::class, WishlistService::class);
        $this->app->bind(MembershipServiceInterface::class, MembershipService::class);
        $this->app->bind(AuctionServiceInterface::class, AuctionService::class);
        $this->app->bind(EvaluateServiceInterface::class, EvaluateService::class);
        $this->app->bind(PointPackageServiceInterface::class, PointPackageService::class);
        $this->app->bind(BannerServiceInterface::class, BannersBannerService::class);
        $this->app->bind(OrderDetailServiceInterface::class, OrderDetailService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(PageStaticServiceInterface::class, PageStaticService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

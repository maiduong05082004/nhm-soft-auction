<?php

namespace App\Providers;

use App\Repositories\AuctionBids\AuctionBidRepository;
use App\Repositories\AuctionBids\AuctionBidRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\Config\ConfigRepository;
use App\Repositories\Config\ConfigRepositoryInterface;
use App\Repositories\MembershipPlan\MembershipPlanRepository;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use App\Repositories\MembershipTransaction\MembershipTransactionRepository;
use App\Repositories\MembershipTransaction\MembershipTransactionRepositoryInterface;
use App\Repositories\MembershipUser\MembershipUserRepository;
use App\Repositories\MembershipUser\MembershipUserRepositoryInterface;
use App\Repositories\TransactionPayment\TransactionPaymentRepository;
use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepository;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Orders\OrderRepository;
use App\Repositories\Orders\OrderRepositoryInterface;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Products\ProductRepositoryInterface;
use App\Repositories\ProductImages\ProductImageRepository;
use App\Repositories\ProductImages\ProductImageRepositoryInterface;
use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\Auctions\AuctionRepositoryInterface;
use App\Repositories\CreditCards\CreditCardRepository;
use App\Repositories\CreditCards\CreditCardRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\OrderDetails\OrderDetailRepository;
use App\Repositories\OrderDetails\OrderDetailRepositoryInterface;
use App\Repositories\Payments\PaymentRepository;
use App\Repositories\Payments\PaymentRepositoryInterface;
use App\Repositories\Wishlist\WishlistRepository;
use App\Repositories\Wishlist\WishlistRepositoryInterface;
use App\Repositories\Evaluates\EvaluateRepository;
use App\Repositories\Evaluates\EvaluateRepositoryInterface;
class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, ProductImageRepository::class);
        $this->app->bind(AuctionRepositoryInterface::class, AuctionRepository::class);
        $this->app->bind(ConfigRepositoryInterface::class, ConfigRepository::class);
        $this->app->bind(TransactionPaymentRepositoryInterface::class, TransactionPaymentRepository::class);
        $this->app->bind(TransactionPointRepositoryInterface::class, TransactionPointRepository::class);
        $this->app->bind(MembershipPlanRepositoryInterface::class, MembershipPlanRepository::class);
        $this->app->bind(MembershipUserRepositoryInterface::class, MembershipUserRepository::class);
        $this->app->bind(MembershipTransactionRepositoryInterface::class, MembershipTransactionRepository::class);
        $this->app->bind(AuctionRepositoryInterface::class, AuctionRepository::class);
        $this->app->bind(AuctionBidRepositoryInterface::class, AuctionBidRepository::class);
        $this->app->bind(CreditCardRepositoryInterface::class, CreditCardRepository::class);
        $this->app->bind(OrderDetailRepositoryInterface::class, OrderDetailRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, WishlistRepository::class);
        $this->app->bind(EvaluateRepositoryInterface::class, EvaluateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Services\Products;

use App\Enums\Product\ProductPaymentMethod;
use App\Enums\Product\ProductState;
use App\Enums\Product\ProductTypeSale;
use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\ProductImages\ProductImageRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\Wishlist\WishlistRepository;
use App\Services\Evaluates\EvaluateService;
use App\Services\Config\ConfigService;
use App\Services\Products\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    protected EvaluateService $evaluateService;
    protected ConfigService $configService;
    protected ProductRepository $productRepository;
    
    public function __construct(
        ProductRepository $productRepo,
        ProductImageRepository $productImageRepo,
        UserRepository $userRepo,
        AuctionRepository $auctionRepo,
        WishlistRepository $wishlistRepo,
        EvaluateService $evaluateService,
        ConfigService $configService
    ) {
        parent::__construct([
            'product' => $productRepo,
            'productImage' => $productImageRepo,
            'user' => $userRepo,
            'auction' => $auctionRepo,
            'wishlist' => $wishlistRepo,
        ]);
        $this->productRepository = $productRepo;
        $this->evaluateService = $evaluateService;
        $this->configService = $configService;
    }

    public function show($product)
    {
        $user = $this->repositories['user']->getAll(['id' => $product->created_by])->first();
        $product->load(['images', 'category']);
        $product_images = $this->repositories['productImage']->getAll(['product_id' => $product->id]);
        $product_category = $this->repositories['product']->getAll(
            ['category_id' => $product->category_id],
            ['images']
        )->where('id', '!=', $product->id)->take(8);
        $auction = $this->repositories['auction']->getAll(['product_id' => $product->id])->first();
        $typeSale = [
            'SALE' => ProductTypeSale::SALE->value,
            'AUCTION' => ProductTypeSale::AUCTION->value,
        ];
        $totalBids = 0;
        $currentPrice = null;
        $auctionData = null;
        if ($auction && (int) $product->type_sale === $typeSale['AUCTION']) {
            $bids = $this->repositories['auction']->getBidsByAuction($auction->id);
            $totalBids = $bids ? $bids->count() : 0;
            if ($totalBids > 0) {
                $highestBid = $this->repositories['auction']->getHighestBid($auction->id);
                $currentPrice = $highestBid ? $highestBid->bid_price : $auction->start_price;
            } else {
                $currentPrice = $auction->start_price;
                $highestBid = null;
            }
            $recentBids = $this->repositories['auction']->getRecentBids($auction->id, 10);
            $auctionData = [
                'auction' => $auction,
                'highest_bid' => $highestBid,
                'total_bids' => $totalBids,
                'current_price' => $currentPrice,
                'min_next_bid' => ($currentPrice ?? 0) + ($auction->step_price ?? 0),
                'recent_bids' => $recentBids,
            ];
        }

        $followersCount = $this->repositories['wishlist']->getAll(['product_id' => $product->id])->count();

        $evaluateStats = $this->evaluateService->getProductRatingStats($product->id);

        $productStateLabel = 'Chưa có thông tin';
        if (isset($product->state)) {
            $productState = ProductState::tryFrom($product->state);
            if ($productState) {
                $productStateLabel = $productState->getLabel($productState);
            }
        }
        if(isset($product->pay_method)) {
            $productPaymentMethod = ProductPaymentMethod::tryFrom($product->pay_method);
            if ($productPaymentMethod) {
                $productPaymentMethodLabel = $productPaymentMethod->getLabel($productPaymentMethod);
            }
        }

        $coinBindProductAuction = $this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 10);
        $priceOneCoin = $this->configService->getConfigValue('PRICE_ONE_COIN', 1000);
        $totalCoinCost = $coinBindProductAuction * $priceOneCoin;

        return compact('product', 'product_images', 'auction', 'user', 'product_category', 'typeSale', 'totalBids', 'currentPrice', 'auctionData', 'followersCount', 'productStateLabel', 'productPaymentMethodLabel', 'coinBindProductAuction', 'priceOneCoin', 'totalCoinCost') + $evaluateStats;
    }

    public function filterProductList($query = [], $page = 1, $perPage = 12)
    {
        $cacheKey = $this->buildCacheKey('products_lists', $query, $page, $perPage);
        // return Cache::remember($cacheKey, 600, function () use ($query, $page, $perPage) {
            return $this->productRepository->getProductByFilter($query, $page, $perPage);
        // });
    }
    public function incrementViewCount ($productId) {
        return $this->productRepository->incrementViewCount($productId);
    }
    private function buildCacheKey(string $prefix, ...$params): string
    {
        $serialized = serialize($params);
        return $prefix . '_' . $serialized;
    }
    
}

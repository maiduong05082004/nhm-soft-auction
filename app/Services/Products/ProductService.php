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
use App\Repositories\Categories\CategoryRepository;
use App\Repositories\Wishlist\WishlistRepository;
use App\Repositories\TransactionPoint\TransactionPointRepository;
use App\Repositories\OrderDetails\OrderDetailRepository;
use App\Repositories\Payments\PaymentRepository;
use App\Repositories\Orders\OrderRepository;
use App\Services\Evaluates\EvaluateService;
use App\Services\Config\ConfigService;
use App\Services\Products\ProductServiceInterface;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

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
        CategoryRepository $categoryRepo,
        EvaluateService $evaluateService,
        ConfigService $configService,
        TransactionPointRepository $transactionPointRepo,
        OrderDetailRepository $orderDetailRepo,
        PaymentRepository $paymentRepo
    ) {
        parent::__construct([
            'product' => $productRepo,
            'productImage' => $productImageRepo,
            'user' => $userRepo,
            'auction' => $auctionRepo,
            'wishlist' => $wishlistRepo,
            'category' => $categoryRepo,
            'transactionPoint' => $transactionPointRepo,
            'order' => app(OrderRepository::class),
            'orderDetail' => $orderDetailRepo,
            'payment' => $paymentRepo,
        ]);
        $this->productRepository = $productRepo;
        $this->evaluateService = $evaluateService;
        $this->configService = $configService;
    }

    public function show($product)
    {
        $user = $this->repositories['user']->getAll(['id' => $product->created_by])->first();
        $userPresent = $this->repositories['user']->getAll(['id' => auth()->id()])->first();
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
            $orderDetail = null;
            $order = null;

            if ($highestBid) {
                $ordersOfProduct = $this->repositories['order']->getAll(['product_id' => $product->id]);
                if ($ordersOfProduct && $ordersOfProduct->count() > 0) {
                    $orderDetailIds = $ordersOfProduct->pluck('order_detail_id')->filter()->values();
                    if ($orderDetailIds->count() > 0) {
                        $orderDetail = $this->repositories['orderDetail']->getAll([
                            'user_id' => $highestBid->user_id,
                        ])->first(function ($od) use ($orderDetailIds) {
                            return $orderDetailIds->contains($od->id);
                        });
                        if ($orderDetail) {
                            $order = $ordersOfProduct->firstWhere('order_detail_id', $orderDetail->id);
                        }
                    }
                }
            }
            $payment = $orderDetail ? $this->repositories['payment']->getAll(['order_detail_id' => $orderDetail->id])->first() : null;

            $auctionData = [
                'auction' => $auction,
                'highest_bid' => $highestBid,
                'total_bids' => $totalBids,
                'current_price' => $currentPrice,
                'min_next_bid' => ($currentPrice ?? 0) + ($auction->step_price ?? 0),
                'recent_bids' => $recentBids,
                'order' => $order,
                'orderDetail' => $orderDetail,
                'payment' => $payment,
            ];
        }

        $followersCount = $this->repositories['wishlist']->getAll(['product_id' => $product->id])->count();

        $evaluateStats = $this->evaluateService->getProductRatingStats($product->id);
        $statUserId = $user->id ?? null;
        $sellerStats = $statUserId ? $this->evaluateService->getUserSellerRatingStats((int) $statUserId) : [
            'sellerTotalReviews' => 0,
            'sellerAverageRating' => 0,
            'sellerRatingDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
        ];

        $productStateLabel = 'Chưa có thông tin';
        if (isset($product->state)) {
            $productState = ProductState::tryFrom($product->state);
            if ($productState) {
                $productStateLabel = $productState->getLabel($productState);
            }
        }
        if (isset($product->pay_method)) {
            $productPaymentMethod = ProductPaymentMethod::tryFrom($product->pay_method);
            if ($productPaymentMethod) {
                $productPaymentMethodLabel = $productPaymentMethod->getLabel($productPaymentMethod);
            }
        }

        $coinBindProductAuction = $this->configService->getConfigValue('COIN_BIND_PRODUCT_AUCTION', 10);
        $priceOneCoin = $this->configService->getConfigValue('PRICE_ONE_COIN', 1000);
        $totalCoinCost = $coinBindProductAuction * $priceOneCoin;

        return compact('product', 'product_images', 'auction', 'user', 'userPresent', 'product_category', 'typeSale', 'totalBids', 'currentPrice', 'auctionData', 'followersCount', 'productStateLabel', 'productPaymentMethodLabel', 'coinBindProductAuction', 'priceOneCoin', 'totalCoinCost') + $evaluateStats + $sellerStats;
    }

    public function filterProductList($query = [], $page = 1, $perPage = 12)
    {
        $paginator = $this->productRepository->getProductByFilter($query, $page, $perPage);

        $collection = $paginator->through(function ($product) {
            $product->price_display = $this->getProductPriceDisplay($product);
            return $product;
        });

        return $collection;
    }

    private function getProductPriceDisplay($product)
    {
        $priceDisplay = '0 ₫';

        if ((int)($product->type_sale ?? 0) === ProductTypeSale::AUCTION->value) {
            $auction = $product->relationLoaded('auction')
                ? $product->auction
                : $this->repositories['auction']->getAuctionByProductId($product->id);

            if ($auction) {
                $highestBid = $this->repositories['auction']->getHighestBid($auction->id);
                $currentPrice = $highestBid ? $highestBid->bid_price : ($auction->start_price ?? 0);
                $priceDisplay = $this->formatPrice($currentPrice);
            }
        } else {
            if (!empty($product->price)) {
                $priceDisplay = $this->formatPrice($product->price);
            } elseif (!empty($product->min_bid_amount) && !empty($product->max_bid_amount)) {
                $priceDisplay = $this->formatPrice($product->min_bid_amount)
                    . ' - '
                    . $this->formatPrice($product->max_bid_amount);
            }
        }

        return $priceDisplay;
    }

    private function formatPrice($price)
    {
        return number_format((float)$price, 0, ',', '.') . ' ₫';
    }

    public function getTreeListCategory()
    {
        $cacheKey = $this->buildCacheKey('product_category');
        return $this->repositories['category']->getTreeList();
    }

    public function incrementViewCount($productId)
    {
        return $this->productRepository->incrementViewCount($productId);
    }

    public function getCountProductByCreatedByAndNearMonthly ($userId)  {
        return $this->productRepository->getCountProductByCreatedByAndNearMonthly($userId);
    }


    private function buildCacheKey(string $prefix, ...$params): string
    {
        $serialized = serialize($params);
        return $prefix . '_' . $serialized;
    }

    public function createProductWithSideEffects(array $data, int $userId): Product
    {
        $typeRaw = $data['type_sale'] ?? ProductTypeSale::SALE->value;
        $typeSale = is_object($typeRaw) && method_exists($typeRaw, 'value') ? $typeRaw->value : (int) $typeRaw;

        $user = $this->repositories['user']->find($userId);
        if (!$user) {
            throw new ServiceException('Không tìm thấy người dùng.');
        }

        // $coinCost = 0;
        // if (!$user->hasRole(RoleConstant::ADMIN->value)) {
        //     $coinCost = (int) ($typeSale === ProductTypeSale::SALE->value
        //         ? $this->configService->getConfigValue(ConfigName::COIN_POST_PRODUCT_SALE->value, 0)
        //         : $this->configService->getConfigValue(ConfigName::COIN_POST_PRODUCT_AUCTION->value, 0));

        //     if ($coinCost > 0 && (int) $user->current_balance < $coinCost) {
        //         throw new ServiceException('Số dư coin của bạn không đủ để đăng sản phẩm.');
        //     }
        // }

        if ($typeSale === ProductTypeSale::SALE->value) {
            $data['min_bid_amount'] = 0;
            $data['max_bid_amount'] = 0;
            $data['start_time'] = null;
            $data['end_time'] = null;
        } else if ($typeSale === ProductTypeSale::AUCTION->value) {
            $data['price'] = $data['max_bid_amount'] ?? 0;
        }

        $images = $data['images'] ?? [];
        unset($data['images']);
        if (isset($data['seo']) && is_array($data['seo'])) {
            $data['seo'] = [
                'title' => $data['seo']['title'] ?? null,
                'description' => $data['seo']['description'] ?? null,
                'keywords' => $data['seo']['keywords'] ?? null,
            ];
        }
        if (isset($data['description']) && is_array($data['description'])) {
            $data['description'] = $data['description']['html'] ?? json_encode($data['description'], JSON_UNESCAPED_UNICODE);
        }
        $data['created_by'] = $userId;

        // return DB::transaction(function () use ($data, $typeSale, $images, $coinCost, $user) {
        return DB::transaction(function () use ($data, $typeSale, $images, $user) {
            /** @var Product $product */
            $product = $this->repositories['product']->insertOne($data);

            $position = 1;
            foreach ($images as $imagePath) {
                $this->repositories['productImage']->insertOne([
                    'product_id' => $product->id,
                    'image_url' => $imagePath,
                    'status' => 'active',
                    'position' => $position,
                ]);
                $position++;
            }

            if ($typeSale === ProductTypeSale::AUCTION->value) {
                $this->repositories['auction']->insertOne([
                    'product_id' => $product->id,
                    'start_price' => $data['min_bid_amount'] ?? 0,
                    'step_price' => $data['step_price'] ?? 10000,
                    'start_time' => $data['start_time'] ?? now(),
                    'end_time' => $data['end_time'] ?? now()->addDays(7),
                    'status' => 'active',
                ]);
            }

            // if ($coinCost > 0 && !$user->hasRole(RoleConstant::ADMIN->value)) {
            //     $user->current_balance = (int) $user->current_balance - $coinCost;
            //     $user->save();
            //     $this->repositories['transactionPoint']->insertOne([
            //         'user_id' => $user->id,
            //         'point' => -$coinCost,
            //         'description' => 'Phí đăng sản phẩm #' . $product->id,
            //     ]);
            // }

            return $product;
        });
    }
}

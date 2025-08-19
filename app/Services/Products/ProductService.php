<?php

namespace App\Services\Products;

use App\Enums\Product\ProductTypeSale;
use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\ProductImages\ProductImageRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\Wishlists\WishlistRepository;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Support\Facades\Cache;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        ProductRepository $productRepo,
        ProductImageRepository $productImageRepo,
        UserRepository $userRepo,
        AuctionRepository $auctionRepo,
        WishlistRepository $wishlistRepo
    ) {
        parent::__construct([
            'product' => $productRepo,
            'productImage' => $productImageRepo,
            'user' => $userRepo,
            'auction' => $auctionRepo,
            'wishlist' => $wishlistRepo,
        ]);
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
            $auctionData = [
                'auction' => $auction,
                'highest_bid' => $highestBid,
                'total_bids' => $totalBids,
                'current_price' => $currentPrice,
                'min_next_bid' => ($currentPrice ?? 0) + ($auction->step_price ?? 0),
            ];
        }


        return compact('product', 'product_images', 'auction', 'user', 'product_category', 'typeSale', 'totalBids', 'currentPrice', 'auctionData');
    }

    public function filterProductList($query = [], $page = 1, $perPage = 12)
    {
        $cacheKey = $this->buildCacheKey('products_lists', $query, $page, $perPage);
        // return Cache::remember($cacheKey, 600, function () use ($query, $page, $perPage) {
            return $this->getRepository('product')->getProductByFilter($query, $page, $perPage);
        // });
    }
    public function incrementViewCount ($productId) {
        return $this->getRepository('product')->incrementViewCount($productId);
    }
    private function buildCacheKey(string $prefix, ...$params): string
    {
        $serialized = serialize($params);
        return $prefix . '_' . $serialized;
    }
    
}

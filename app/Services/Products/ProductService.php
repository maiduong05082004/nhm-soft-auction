<?php

namespace App\Services\Products;

use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\ProductImages\ProductImageRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Auctions\AuctionRepository;
use App\Repositories\Categories\CategoryRepository;
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
        WishlistRepository $wishlistRepo,
        CategoryRepository $categoryRepo
    ) {
        parent::__construct([
            'product' => $productRepo,
            'productImage' => $productImageRepo,
            'user' => $userRepo,
            'auction' => $auctionRepo,
            'wishlist' => $wishlistRepo,
            'category' => $categoryRepo
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

        return compact('product', 'product_images', 'auction', 'user', 'product_category');
    }

    public function filterProductList($query = [], $page = 1, $perPage = 12)
    {   
        $cacheKey = $this->buildCacheKey('products_lists', $query, $page, $perPage);
        return Cache::remember($cacheKey, 600, function () use ($query, $page, $perPage) {
            return $this->getRepository('product')->getProductByFilter($query, $page, $perPage);
        });

    }

    public function getTreeListCategory ( ) {
        $cacheKey = $this->buildCacheKey('product_category');
        return $this->repositories['category']->getTreeList();
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

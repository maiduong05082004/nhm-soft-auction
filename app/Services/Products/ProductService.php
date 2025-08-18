<?php

namespace App\Services\Products;

use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\ProductImages\ProductImageRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Auctions\AuctionRepository;
use App\Services\Products\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        ProductRepository $productRepo,
        ProductImageRepository $productImageRepo,
        UserRepository $userRepo,
        AuctionRepository $auctionRepo
    ) {
        parent::__construct([
            'product' => $productRepo,
            'productImage' => $productImageRepo,
            'user' => $userRepo,
            'auction' => $auctionRepo,
        ]);
    }

    public function show($product){
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
}

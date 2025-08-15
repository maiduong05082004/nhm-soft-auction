<?php

namespace App\Services\Products;

use App\Models\Auction;
use App\Models\Product;
use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\Products\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct([
            'product' => $productRepo,
        ]);
    }
    public function show(Product $product){
        $user = User::where('id', $product->created_by)->first();
		$product->load(['images', 'category']);
		$product_images = ProductImage::where('product_id', $product->id)->get();
        $product_category = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)
            ->get();
		$auction = Auction::where('product_id', $product->id)->first();
		return view('pages.products.product-details', compact('product', 'product_images', 'auction', 'user', 'product_category'));
    }
}

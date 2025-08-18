<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Products\ProductServiceInterface;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }
    
    public function show(Product $product)
    {
        $data = $this->productService->show($product);
        return view('pages.products.product-details', $data);
    }
}
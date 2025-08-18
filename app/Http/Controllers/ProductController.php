<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    protected $categoryService;

    public function __construct(ProductServiceInterface $productService, CategoryServiceInterface $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        
    }

    public function show(Product $product)
    {
        $data = $this->productService->show($product);
        $this->productService->incrementViewCount($product['id']);
        return view('pages.products.product-details', $data);
    }

    public function list(Request $req)
    {
        $name = $req->input('product_name');
        $type = $req->input('product_type');
        $min_price = $req->input("price_min");
        $max_price = $req->input("price_max");
        $orderBy = $req->input('sort_by');
        $categoryId = $req->input('category_id');
        $state = $req->input('state');


        $query = [];

        if (!empty($name)) {
            $query['name'] = $name;
        }
        if (!empty($type)) {
            $query['type'] = $type;
        }

        if (!empty($min_price)) {
            $query['min_price'] = $min_price;
        }
        if (!empty($max_price)) {
            $query['max_price'] = $max_price;
        }
        if (!empty($orderBy)) {
            $query['orderBy'] = $orderBy;
        }
        if (!empty($categoryId)) {
            $query['categoryId'] = $categoryId;
        }

        if (!empty($state)) {
            $query['state'] = $state;
        }
        
        $products = $this->productService->filterProductList($query, 1, 12);
        $products->appends($req->query());
        $categories = $this->categoryService->getAll('category');
        return view('pages.products.list', compact('products', 'categories'));
    }
}

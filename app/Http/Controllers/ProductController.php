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
        $page = $req->input('page', 1);
        $category = null;
        $filters = [
            'name'       => $req->input('product_name'),
            'type'       => $req->input('product_type'),
            'min_price'  => $req->input('price_min'),
            'max_price'  => $req->input('price_max'),
            'orderBy'    => $req->input('sort_by'),
            'categoryId' => $req->input('category_id'),
            'state'      => $req->input('state'),
        ];

        if (!empty($req->input('category_id'))) {
            $category = $this->categoryService->getById('category', $req->input('category_id'));
        }

        $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

        $products = $this->productService->filterProductList($filters, $page, 16);
        $products->appends($req->query());
        $categories = $this->productService->getTreeListCategory();
        return view('pages.products.list', compact('products', 'categories', 'category'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Category\CategoryServiceInterface;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $productService;
    protected $categoryService;
    
    public function __construct (ProductServiceInterface $productService, CategoryServiceInterface $categoryService) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index()  {
        $products = $this->productService->getAll('product');
        $categories = $this->categoryService->getAll('category');
        // dd($categories);
        // $product::paginate(4);
        // dd($products);
        return view('pages.dashboard', compact('products', 'categories'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Articles\ArticleService;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $productService;
    protected $categoryService;
    protected $articleService;
    public function __construct(ProductServiceInterface $productService, CategoryServiceInterface $categoryService, ArticleService $articleService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->articleService = $articleService;
    }

    public function index()
    {
        $query_section1[''] = '';
        $query_section2['orderBy'] = 'view_desc';
        $query_section3['is_hot'] = 'true';
        $query_section4['type'] = 'auction';
        $products1 = $this->productService->filterProductList($query_section1, 1, 10);
        $products2 = $this->productService->filterProductList($query_section2, 1, 10);
        $products3 = $this->productService->filterProductList($query_section3, 1, 10);
        $products4 = $this->productService->filterProductList($query_section4, 1, 10);
        $categories = $this->productService->getTreeListCategory();
        $articles = $this->articleService->getArticlesList([], 1, 12);

        return view('pages.dashboard', compact('products1', 'products2', 'products3', 'products4' ,'categories', 'articles'));
    }
}

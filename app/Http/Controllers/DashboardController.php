<?php

namespace App\Http\Controllers;

use App\Services\Articles\ArticleService;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Products\ProductServiceInterface;
use App\Services\Cart\CartServiceInterface;
use App\Services\Wishlist\WishlistServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $productService;
    protected $categoryService;
    protected $articleService;
    protected CartServiceInterface $cartService;
    protected WishlistServiceInterface $wishlistService;
    public function __construct(
        ProductServiceInterface $productService,
        CategoryServiceInterface $categoryService,
        ArticleService $articleService,
        CartServiceInterface $cartService,
        WishlistServiceInterface $wishlistService
    )
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->articleService = $articleService;
        $this->cartService = $cartService;
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        $query_section1['is_hot'] = 'true';
        $query_section2['orderBy'] = 'view_desc';
        $query_section3['is_new'] = 'true';
        $query_section4['type'] = 'auction';
        $products1 = $this->productService->filterProductList($query_section1, 1, 12);
        $products2 = $this->productService->filterProductList($query_section2, 1, 12);
        $products3 = $this->productService->filterProductList($query_section3, 1, 12);
        $products4 = $this->productService->filterProductList($query_section4, 1, 12);
        $categories = $this->productService->getTreeListCategory();
        $articles = $this->articleService->getArticlesList([], 1, 12);

        $headerCartCount = 0;
        $headerWishlistCount = 0;
        if (auth()->check()) {
            $cartSummary = $this->cartService->getCartSummary(auth()->id());
            if (!empty($cartSummary['success']) && !empty($cartSummary['data'])) {
                $headerCartCount = (int) ($cartSummary['data']['count'] ?? 0);
            }

            $wishSummary = $this->wishlistService->getSummary(auth()->id());
            if (!empty($wishSummary['success']) && !empty($wishSummary['data'])) {
                $headerWishlistCount = (int) ($wishSummary['data']['count'] ?? 0);
            }
        }

        return view('pages.dashboard', compact(
            'products1', 'products2', 'products3', 'products4', 'categories', 'articles',
            'headerCartCount', 'headerWishlistCount'
        ));
    }
}

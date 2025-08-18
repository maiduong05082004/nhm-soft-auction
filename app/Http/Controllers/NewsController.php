<?php

namespace App\Http\Controllers;

use App\Services\Articles\ArticleServiceInterface;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function list(Request $request)
    {
        $q = $request->input('q');
        $category = $request->input('danh-muc');
        $orderBy = $request->input('sap-xep');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);

        $query = [];

        if (!empty($q)) {
            $query['search'] = $q;
        }

        if (!empty($category)) {
            $query['category'] = $category;
        }

        if(!empty($orderBy)){
            $query['orderBy'] = $orderBy;
        }

        $articles = $this->articleService->getArticlesList($query, $page, $perPage);

        $articles->appends($request->query());

        $categories = $this->articleService->getAllCategories();

        return view('pages.news.list', compact('articles', 'categories', 'q', 'category'));
    }

    public function article(string $slug)
    {
        $article = $this->articleService->getBySlug($slug);

        if (!$article) {
            abort(404, 'Bài viết không tồn tại');
        }

        $this->articleService->incrementViewCount($article->id);
        $related_articles = $this->articleService->getRelatedArticles(
            $article->category_article_id,
            $article->id,
            5
        );

        return view('pages.news.article', compact('article', 'related_articles'));
    }

    public function category(Request $request, string $categorySlug)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);

        $category = $this->articleService->getCategoryBySlug($categorySlug);

        if (!$category) {
            abort(404, 'Danh mục không tồn tại');
        }

        $articles = $this->articleService->getArticlesByCategory($category->id, $page, $perPage);

        return view('pages.news.category', compact('articles', 'category'));
    }

    public function popular(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);
        $timeRange = $request->input('time', 'month');

        $articles = $this->articleService->getPopularArticles($timeRange, $page, $perPage);

        return view('pages.news.popular', compact('articles', 'timeRange'));
    }
}

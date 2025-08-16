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

    
    public function article(string $slug)
    {   
        $article = $this->articleService->getBySlug($slug);
        $related_articles = $this->articleService->getRelatedArticle($article->category_article_id, $article->id );
        return view('pages.news.article', compact('article', 'related_articles'));
    }
}

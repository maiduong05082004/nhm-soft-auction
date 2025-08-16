<?php

namespace App\Services\Articles;
use App\Services\BaseService;
use App\Services\Articles\ArticleServiceInterface;
use App\Repositories\Articles\ArticleRepository;

class ArticleService extends BaseService implements ArticleServiceInterface
{    
    public function __construct(ArticleRepository $articleRepository)
    {
        parent::__construct([
            'article' => $articleRepository
        ]);
    }

    public function getBySlug(string $slug)
    {
        return $this->getRepository('article')->getBySlug($slug);
    }

    public function getRelatedArticle($categoryId, $currentArticleId)
    {
        return $this->getRepository('article')->getRelatedArticle($categoryId, $currentArticleId);
    }
}

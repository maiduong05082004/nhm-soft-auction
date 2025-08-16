<?php

namespace App\Repositories\Articles;

use App\Models\Article;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{
    public function getModel(): string
    {
        return Article::class;
    }

    public function getBySlug(string $slug) : ?Article
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getRelatedArticle($categoryId, $currentArticleId) : ?Collection{
        return $this->model->where('category_article_id', $categoryId)->where('id','<>',$currentArticleId)->limit(10)->get();
    }
}

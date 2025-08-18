<?php

namespace App\Repositories\Articles;

use App\Models\Article;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface extends BaseRepositoryInterface
{
    public function getBySlug(string $slug): ?Article;
    public function getRelatedArticles($categoryId, $currentArticleId, $limit = 5): Collection;
    public function getArticlesList($query = [], $page = 1, $perPage = 12): LengthAwarePaginator;
    public function getByCategory($categoryId, $page = 1, $perPage = 12): LengthAwarePaginator;
    public function searchArticles($keyword, $page = 1, $perPage = 12): LengthAwarePaginator;
    public function getPopularArticles($timeRange = 'month', $page = 1, $perPage = 12): LengthAwarePaginator;
    public function getLatest($limit = 10): Collection;
    public function countByCategory(): array;
    public function incrementViewCount($articleId);
    public function getRandomArticles($limit = 5): Collection;
}

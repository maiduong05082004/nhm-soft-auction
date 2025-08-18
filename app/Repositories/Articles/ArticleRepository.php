<?php

namespace App\Repositories\Articles;

use App\Models\Article;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{
    public function getModel(): string
    {
        return Article::class;
    }

    public function getBySlug(string $slug): ?Article
    {
        return $this->model->with(['category', 'author'])
            ->where('slug', $slug)
            ->where('publish_time', '<=', now())
            ->first();
    }

    public function getRelatedArticles($categoryId, $currentArticleId, $limit = 5): Collection
    {
        return $this->model->with(['category', 'author'])
            ->where('category_article_id', $categoryId)
            ->where('id', '<>', $currentArticleId)
            ->where('publish_time', '<=', now())
            ->limit($limit)
            ->get();
    }

    public function getArticlesList($query = [], $page = 1, $perPage = 12): LengthAwarePaginator
    {
        $builder = $this->model->with(['category', 'author'])
            ->where('status', '=', 'published')
            ->where('publish_time', '<=', now());

        if (!empty($query['search'])) {
            $keyword = $query['search'];
            $builder->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if (!empty($query['category'])) {
            if (is_numeric($query['category'])) {
                $builder->where('category_article_id', $query['category']);
            } else {
                $builder->whereHas('category', function ($q) use ($query) {
                    $q->where('slug', $query['category']);
                });
            }
        }

        if (!empty($query['orderBy'])) {
            if ($query['orderBy'] == 'view') {
                $builder->orderBy('view', 'desc');
            } else if ($query['orderBy'] == 'sort') {
                $builder->orderByRaw("
                        CASE 
                            WHEN publish_time >= ? THEN 0 
                            ELSE 1 
                        END
                    ", [now()->subWeek()])
                    ->orderBy('sort', 'asc');
            }
        }

        if (!empty($query['author'])) {
            $builder->where('user_id', $query['author']);
        }

        return $builder->paginate($perPage, ['*'], 'page', $page);
    }

    public function incrementViewCount($articleId): bool
    {
        return $this->model->where('id', $articleId)->increment('view');
    }
    public function getByCategory($categoryId, $page = 1, $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['category', 'author'])
            ->where('category_article_id', $categoryId)
            ->where('publish_time', '<=', now())
            ->paginate($perPage, ['*'], 'page', $page);
    }


    public function searchArticles($keyword, $page = 1, $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['category', 'author'])
            ->where('is_published', true)
            ->where('publish_time', '<=', now())
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('excerpt', 'like', "%{$keyword}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getPopularArticles($timeRange = 'month', $page = 1, $perPage = 12): LengthAwarePaginator
    {
        $days = match ($timeRange) {
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30
        };

        return $this->model->with(['category', 'author'])
            ->where('publish_time', '<=', now())
            ->orderBy('publish_time', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getLatest($limit = 10): Collection
    {
        return $this->model->with(['category', 'author'])
            ->where('publish_time', '<=', now())
            ->limit($limit)
            ->get();
    }

    public function countByCategory(): array
    {
        return $this->model->select('category_article_id', DB::raw('count(*) as total'))
            ->where('publish_time', '<=', now())
            ->groupBy('category_article_id')
            ->pluck('total', 'category_article_id')
            ->toArray();
    }

    public function getRandomArticles($limit = 5): Collection
    {
        return $this->model->with(['category', 'author'])
            ->where('publish_time', '<=', now())
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}

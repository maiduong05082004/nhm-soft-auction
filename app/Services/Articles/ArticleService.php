<?php

namespace App\Services\Articles;

use App\Services\BaseService;
use App\Services\Articles\ArticleServiceInterface;
use App\Repositories\Articles\ArticleRepository;
use App\Repositories\CategoryArticle\CategoryArticleRepository;
use Illuminate\Support\Facades\Cache;

class ArticleService extends BaseService implements ArticleServiceInterface
{
    public function __construct(
        ArticleRepository $articleRepository,
        CategoryArticleRepository $categoryRepository
    ) {
        parent::__construct([
            'article' => $articleRepository,
            'category' => $categoryRepository
        ]);
    }

    public function getBySlug(string $slug)
    {
        return $this->getRepository('article')->getBySlug($slug);
    }

    public function getRelatedArticles($categoryId, $currentArticleId, $limit = 5)
    {
        return $this->getRepository('article')->getRelatedArticles($categoryId, $currentArticleId, $limit);
    }

    public function getArticlesList($query = [], $page = 1, $perPage = 12)
    {
        $cacheKey = $this->buildCacheKey('articles_list', $query, $page, $perPage);
        return Cache::remember($cacheKey, 600, function () use ($query, $page, $perPage) {
            return $this->getRepository('article')->getArticlesList($query, $page, $perPage);
        });
    }


    public function getAllCategories()
    {
        return $this->getRepository('category')->getAllActive();
    }

    private function buildCacheKey(string $prefix, ...$params): string
    {
        $serialized = serialize($params);
        return $prefix . '_' . $serialized;
    }

    public function incrementViewCount($articleId)
    {
        return $this->getRepository('article')->incrementViewCount($articleId);
    }


    public function getPreviousArticle($currentId, $categoryId = null)
    {
        return $this->getRepository('article')->getPreviousArticle($currentId, $categoryId);
    }


    public function getNextArticle($currentId, $categoryId = null)
    {
        return $this->getRepository('article')->getNextArticle($currentId, $categoryId);
    }


    public function getCategoryBySlug($slug)
    {
        return $this->getRepository('category')->getBySlug($slug);
    }

    /**
     * Lấy bài viết theo danh mục
     */
    public function getArticlesByCategory($categoryId, $page = 1, $perPage = 12)
    {
        return $this->getRepository('article')->getByCategory($categoryId, $page, $perPage);
    }

    /**
     * Tìm kiếm bài viết
     */
    public function searchArticles($keyword, $page = 1, $perPage = 12)
    {
        return $this->getRepository('article')->searchArticles($keyword, $page, $perPage);
    }

    /**
     * Lấy bài viết phổ biến
     */
    public function getPopularArticles($timeRange = 'month', $page = 1, $perPage = 12)
    {
        return $this->getRepository('article')->getPopularArticles($timeRange, $page, $perPage);
    }

    /**
     * Lưu lịch sử tìm kiếm
     */
    public function saveSearchHistory($keyword, $ipAddress)
    {
        // Có thể implement sau nếu cần
        return true;
    }

    /**
     * Lấy bài viết mới nhất
     */
    public function getLatestArticles($limit = 10)
    {
        return $this->getRepository('article')->getLatest($limit);
    }

    /**
     * Lấy bài viết nổi bật
     */
    public function getFeaturedArticles($limit = 5)
    {
        return $this->getRepository('article')->getFeatured($limit);
    }

    /**
     * Lấy bài viết theo tag
     */
    public function getArticlesByTag($tagSlug, $page = 1, $perPage = 12)
    {
        return $this->getRepository('article')->getByTag($tagSlug, $page, $perPage);
    }
}

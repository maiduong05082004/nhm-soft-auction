<?php

namespace App\Services\Articles;

use App\Services\BaseServiceInterface;

interface ArticleServiceInterface extends BaseServiceInterface
{
    public function getBySlug(string $slug);
    public function getRelatedArticles($categoryId, $currentArticleId, $limit = 5);
    public function getArticlesList($query = [], $page = 1, $perPage = 12);
    public function getAllCategories();
    public function incrementViewCount($articleId);
    public function getPreviousArticle($currentId, $categoryId = null);
    public function getNextArticle($currentId, $categoryId = null);
    public function getCategoryBySlug($slug);
    public function getArticlesByCategory($categoryId, $page = 1, $perPage = 12);
    public function searchArticles($keyword, $page = 1, $perPage = 12);
    public function getPopularArticles($timeRange = 'month', $page = 1, $perPage = 12);
    public function saveSearchHistory($keyword, $ipAddress);
    public function getLatestArticles($limit = 10);
    public function getFeaturedArticles($limit = 5);
    public function getArticlesByTag($tagSlug, $page = 1, $perPage = 12);
    public function getTreeListCategory();
}

<?php

namespace App\Repositories\Articles;

use App\Repositories\BaseRepositoryInterface;

interface ArticleRepositoryInterface extends BaseRepositoryInterface
{
    public function getBySlug(string $slug);
    public function getRelatedArticle($categoryId, $currentArticleId);
}
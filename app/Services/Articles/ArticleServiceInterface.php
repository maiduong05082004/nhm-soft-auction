<?php

namespace App\Services\Articles;

use App\Services\BaseServiceInterface;

interface ArticleServiceInterface extends BaseServiceInterface
{
    public function getBySlug(string $slug);

    public function getRelatedArticle($categoryId, $currentArticleId);
}

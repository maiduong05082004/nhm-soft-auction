<?php

namespace App\Repositories\CategoryArticle;

use App\Repositories\BaseRepositoryInterface;

interface CategoryArticleRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllActive();
    public function getBySlug($slug);
    public function getTreeList();
}

<?php

namespace App\Repositories\Categories;

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel(): string
    {
        return Category::class;
    }

    public function getTreeList()
    {
        return $this->model->whereNull('parent_id')->orderBy('name')->get();
    }
}

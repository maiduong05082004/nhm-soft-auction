<?php

namespace App\Services\Category;

use App\Repositories\Categories\CategoryRepository;
use App\Services\BaseService;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    protected CategoryRepository $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct ([
            'category' => $categoryRepository
        ]);
    }
}

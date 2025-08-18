<?php

namespace App\Repositories\CategoryArticle;

use App\Models\CategoryArticle;
use App\Repositories\BaseRepository;


class CategoryArticleRepository extends BaseRepository implements CategoryArticleRepositoryInterface{
    public function getModel(): string
    {
        return CategoryArticle::class;
    }

    public function getAllActive()
    {
        return $this->model->where('status',1)->get();
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug',$slug)->first();
    }
}
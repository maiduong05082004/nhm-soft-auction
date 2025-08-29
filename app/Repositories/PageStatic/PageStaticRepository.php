<?php

namespace App\Repositories\PageStatic;

use App\Enums\CommonConstant;
use App\Models\PageStatic;
use App\Repositories\BaseRepository;

class PageStaticRepository extends BaseRepository implements PageStaticRepositoryInterface
{
    public function getModel(): string
    {
        return PageStatic::class;
    }

    public function getBySlug($slug)
    {
        return $this->model
            ->where('slug', '=', $slug)
            ->where('published_at', '<=', now())
            ->where('status', CommonConstant::ACTIVE)
            ->get();
    }
    public function getAllByStatusAndPublishedAt()
    {
        return $this->model->where('status', CommonConstant::ACTIVE)->where('published_at', '<=', now())->get();
    }
}

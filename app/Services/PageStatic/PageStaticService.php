<?php

namespace App\Services\PageStatic;

use App\Exceptions\ServiceException;
use App\Repositories\PageStatic\PageStaticRepository;
use App\Services\BaseService;

class PageStaticService extends BaseService implements PageStaticServiceInterface
{
    protected $pageStaticRepository;

    public function __construct(PageStaticRepository $pageStaticRepository)
    {
        parent::__construct([
            'pageStatic' => $pageStaticRepository
        ]);
    }

    public function getBySlug($slug) {
        return $this->getRepository('pageStatic')->getBySlug($slug);
    }
}

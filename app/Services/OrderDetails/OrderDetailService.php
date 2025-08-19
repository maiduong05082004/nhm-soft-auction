<?php

namespace App\Services\OrderDetails;

use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\OrderDetails\OrderDetailRepository;

class OrderDetailService extends BaseService implements OrderDetailServiceInterface
{
    public function __construct(OrderDetailRepository $orderDetailRepo, ProductRepository $productRepo)
    {
        parent::__construct([
            'orderDetail' => $orderDetailRepo,
            'product' => $productRepo,
        ]);
    }
}
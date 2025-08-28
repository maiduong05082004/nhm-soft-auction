<?php

namespace App\Services\OrderDetails;

use App\Services\BaseService;
use App\Repositories\Products\ProductRepository;
use App\Repositories\OrderDetails\OrderDetailRepository;
use App\Repositories\Payments\PaymentRepository;

class OrderDetailService extends BaseService implements OrderDetailServiceInterface
{
    public function __construct(OrderDetailRepository $orderDetailRepo, ProductRepository $productRepo, PaymentRepository $paymentRepo)
    {
        parent::__construct([
            'orderDetail' => $orderDetailRepo,
            'product' => $productRepo,
            'payment' => $paymentRepo
        ]);
    }

    public function getPaymentByUserId($userId) {
        return $this->getRepository('payment')->query()->where('user_id', $userId)->orderBy('created_at','desc')->get();
    }
}
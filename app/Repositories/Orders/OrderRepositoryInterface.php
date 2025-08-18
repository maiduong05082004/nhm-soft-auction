<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepositoryInterface;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrder(array $orderData);
    public function getOrderWithDetails($orderId);
    public function updateOrderStatus($orderId, $status);
}
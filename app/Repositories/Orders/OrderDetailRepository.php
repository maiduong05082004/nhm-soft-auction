<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepository;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function getModel(): string
    {
        return Order::class;
    }

    public function createOrder(array $orderData)
    {
        return DB::transaction(function () use ($orderData) {
            $order = $this->model->create($orderData);
            return $order;
        });
    }

    public function getOrderWithDetails($orderId)
    {
        return $this->model->with(['orderDetails.product', 'payment'])->find($orderId);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = $this->model->find($orderId);
        if ($order) {
            $order->update(['status' => $status]);
            return $order;
        }
        return null;
    }
}

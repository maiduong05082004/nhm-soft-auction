<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepository;
use App\Models\Order;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getModel(): string
    {
        return Order::class;
    }
}

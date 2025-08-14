<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepository;
use App\Models\Order;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function getModel(): string
    {
        return Order::class;
    }
}

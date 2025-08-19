<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepository;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function getModel(): string
    {
        return Order::class;
    }
}

<?php

namespace App\Repositories\Orders;

use App\Repositories\BaseRepository;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getModel(): Model
    {
        return new Order();
    }
}

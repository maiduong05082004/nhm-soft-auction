<?php

namespace App\Repositories\OrderDetails;

use App\Repositories\BaseRepository;
use App\Models\OrderDetail;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function getModel(): string
    {
        return OrderDetail::class;
    }
}
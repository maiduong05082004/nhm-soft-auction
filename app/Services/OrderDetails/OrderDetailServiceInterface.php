<?php

namespace App\Services\OrderDetails;

use App\Services\BaseServiceInterface;

interface OrderDetailServiceInterface extends BaseServiceInterface
{

    public function getPaymentByUserId($userId);
}

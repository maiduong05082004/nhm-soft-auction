<?php

namespace App\Services\Payments;

use App\Services\BaseServiceInterface;

interface PaymentServiceInterface extends BaseServiceInterface
{
    public function confirmPaymentBySeller(int $orderDetailId, int $sellerUserId): bool;
}



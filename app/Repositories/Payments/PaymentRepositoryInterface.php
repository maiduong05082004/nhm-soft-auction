<?php

namespace App\Repositories\Payments;

use App\Repositories\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function createPayment(array $paymentData);
    public function getPaymentByOrderId($orderId);
    public function updatePaymentStatus($paymentId, $status);
}
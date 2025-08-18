<?php

namespace App\Repositories\Payments;

use App\Repositories\BaseRepository;
use App\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function getModel(): string
    {
        return Payment::class;
    }

    public function createPayment(array $paymentData)
    {
        return $this->model->create($paymentData);
    }

    public function getPaymentByOrderId($orderId)
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    public function updatePaymentStatus($paymentId, $status)
    {
        $payment = $this->model->find($paymentId);
        if ($payment) {
            $payment->update(['status' => $status]);
            return $payment;
        }
        return null;
    }
}
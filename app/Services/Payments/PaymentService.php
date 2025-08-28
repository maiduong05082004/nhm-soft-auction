<?php

namespace App\Services\Payments;

use App\Repositories\Payments\PaymentRepository;
use App\Repositories\Orders\OrderRepository;
use App\Services\BaseService;

class PaymentService extends BaseService implements PaymentServiceInterface
{
    public function __construct(
        PaymentRepository $paymentRepo,
        OrderRepository $orderRepo
    ) {
        parent::__construct([
            'payment' => $paymentRepo,
            'order' => $orderRepo,
        ]);
    }

    public function confirmPaymentBySeller($orderDetailId, $sellerUserId): bool
    {
        $orders = $this->getRepository('order')->getAll(['order_detail_id' => $orderDetailId], ['product']);
        $isOwner = $orders->first(function ($ord) use ($sellerUserId) {
            return (int) ($ord->product->created_by ?? 0) === (int) $sellerUserId;
        }) !== null;

        if (!$isOwner) {
            return false;
        }

        $payment = $this->getRepository('payment')->getAll(['order_detail_id' => $orderDetailId])->first();
        if (!$payment) {
            return false;
        }
        if (!empty($payment->confirmation_at)) {
            return true;
        }

        $this->getRepository('payment')->updateOne($payment->id, [
            'confirmation_at' => now()->toDateTimeString(),
        ]);
        return true;
    }
}



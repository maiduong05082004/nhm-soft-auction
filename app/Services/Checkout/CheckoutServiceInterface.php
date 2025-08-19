<?php

namespace App\Services\Checkout;

use App\Services\BaseServiceInterface;

interface CheckoutServiceInterface extends BaseServiceInterface
{
    public function processCheckout(int $userId, array $checkoutData): array;
    public function getOrderDetails(int $orderId): array;
    public function confirmPayment(int $orderId): array;
    public function hasCreditCardConfig(): bool;
    public function buildVietQrUrl(object $orderDetail, ?object $payment): string;
}
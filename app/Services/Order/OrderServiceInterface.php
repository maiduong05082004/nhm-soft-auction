<?php

namespace App\Services\Order;

interface OrderServiceInterface
{
    public function processCheckout(int $userId, array $checkoutData): array;
    public function getOrderDetails(int $orderId): array;
    public function confirmPayment(int $orderId): array;
}

<?php

namespace App\Services\Orders;

use App\Models\OrderDetail;
use App\Services\BaseServiceInterface;

interface OrderServiceInterface extends BaseServiceInterface
{

    public function calculateSubtotal(array $items): float;
    public function calculateTotal(array $items, float $shippingFee = 0): float;
    public function formatCurrency(float $amount): string;
    public function createOrder(array $data);
    public function updateOrder($id, array $data);
    public function getOrderById($id);
    public function getAllOrders(array $conditions = []);
    public function afterCreate(OrderDetail $orderDetail, string $paymentMethod): void;
    public function processCheckout(int $userId, array $checkoutData): array;
    public function getOrderDetails(int $orderId): array;
    public function confirmPayment(int $orderId): array;
}

<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Services\ServiceInterface;

interface OrderServiceInterface extends ServiceInterface
{

    public function calculateSubtotal(array $items): float;

    public function calculateTotal(array $items, float $shippingFee = 0): float;

    public function formatCurrency(float $amount): string;

    public function createOrder(array $data);

    public function updateOrder($id, array $data);

    public function getOrderById($id);

    public function getAllOrders(array $conditions = []);

    public function afterCreate(Order $order, string $paymentMethod): void;
}
<?php

namespace App\Services\PointPackages;

use App\Enums\Transactions\TransactionPaymentStatus;
use App\Models\TransactionPayment;
use App\Services\BaseServiceInterface;

interface PointPackageServiceInterface extends BaseServiceInterface
{
    public function getAllPointPackage();
    public function getPointPackageById($packageId);
    public function createTransactionPayment($dataTransfer, $userId);
    public function getTransactionPaymentByType($type);
    public function confirmPointTransaction(TransactionPayment $record, TransactionPaymentStatus $status);
}

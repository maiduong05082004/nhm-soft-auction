<?php

namespace App\Repositories\TransactionPayment;

use App\Enums\Transactions\TransactionPaymentType;
use App\Repositories\BaseRepositoryInterface;

interface TransactionPaymentRepositoryInterface extends BaseRepositoryInterface
{

    public function sumTransTypeByUserId(TransactionPaymentType $type, $userId);
}

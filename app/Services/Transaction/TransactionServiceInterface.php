<?php

namespace App\Services\Transaction;

use App\Services\BaseServiceInterface;

interface TransactionServiceInterface extends BaseServiceInterface
{
    public function getQueryTransactionMembershipAdmin();
}

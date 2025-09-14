<?php

namespace App\Services\Transaction;

use App\Enums\Membership\MembershipTransactionStatus;
use App\Services\BaseServiceInterface;

interface TransactionServiceInterface extends BaseServiceInterface
{
    public function getQueryTransactionMembershipAdmin();

    public function confirmMembershipTransactionForwebhook($orderCode, $status);

}

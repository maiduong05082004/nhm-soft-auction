<?php

namespace App\Enums\Transactions;

enum TransactionPaymentType: int
{
    case BUY_PRODUCT = 1;
    case BID_PRODUCT = 2;
    case RECHANGE_POINT = 3;

}

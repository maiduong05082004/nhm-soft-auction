<?php

namespace App\Services\Transaction;

use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Services\BaseService;

class TransactionService extends BaseService implements TransactionServiceInterface
{
    public function __construct(TransactionPaymentRepositoryInterface $transactionPaymentRepository, TransactionPointRepositoryInterface $transactionPointRepository)
    {
        parent::__construct([
            'transactionPaymentRepository' => $transactionPaymentRepository,
            'transactionPointRepository' => $transactionPointRepository,
        ]);
    }


}

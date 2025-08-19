<?php

namespace App\Repositories\Payments;

use App\Repositories\BaseRepository;
use App\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function getModel(): string
    {
        return Payment::class;
    }
}
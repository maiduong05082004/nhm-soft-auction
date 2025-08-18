<?php

namespace App\Repositories\TransactionPoint;

use App\Repositories\BaseRepositoryInterface;

interface TransactionPointRepositoryInterface extends BaseRepositoryInterface
{
    public function sumTransByUserId($userId);
}

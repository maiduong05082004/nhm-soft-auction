<?php

namespace App\Repositories\MembershipTransaction;

use App\Models\MembershipTransaction;
use App\Repositories\BaseRepository;

class MembershipTransactionRepository extends BaseRepository implements MembershipTransactionRepositoryInterface
{

    public function getModel(): string
    {
        return MembershipTransaction::class;
    }
}

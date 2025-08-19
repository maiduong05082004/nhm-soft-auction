<?php

namespace App\Repositories\MembershipUser;

use App\Models\MembershipUser;
use App\Repositories\BaseRepository;

class MembershipUserRepository extends BaseRepository implements MembershipUserRepositoryInterface
{
    public function getModel(): string
    {
       return MembershipUser::class;
    }
}

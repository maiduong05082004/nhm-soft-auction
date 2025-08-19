<?php

namespace App\Repositories\MembershipPlan;

use App\Models\MembershipPlan;
use App\Repositories\BaseRepository;

class MembershipPlanRepository extends BaseRepository implements MembershipPlanRepositoryInterface
{
    public function getModel(): string
    {
        return MembershipPlan::class;
    }
}

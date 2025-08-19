<?php

namespace App\Services\Membership;

use App\Services\BaseServiceInterface;

interface MembershipServiceInterface extends BaseServiceInterface
{
    public function getAllMembershipPlan();

    public function getMembershipPlanById($id);
}

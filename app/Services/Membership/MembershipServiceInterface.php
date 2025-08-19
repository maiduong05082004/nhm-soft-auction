<?php

namespace App\Services\Membership;

use App\Models\MembershipUser;
use App\Services\BaseServiceInterface;

interface MembershipServiceInterface extends BaseServiceInterface
{
    public function getAllMembershipPlan();

    public function getMembershipPlanById($id);

    public function createMembershipForUser($userId, $membershipPlan, $dataTransfer): bool;

    public function reActivateMembershipForUser(MembershipUser $membershipUser): bool;

    public function validateActiveMembership(MembershipUser $item): bool;
}

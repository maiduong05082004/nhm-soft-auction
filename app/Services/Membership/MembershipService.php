<?php

namespace App\Services\Membership;

use App\Enums\CommonConstant;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use App\Repositories\MembershipTransaction\MembershipTransactionRepositoryInterface;
use App\Repositories\MembershipUser\MembershipUserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class MembershipService extends BaseService implements MembershipServiceInterface
{
    public function __construct(
        MembershipPlanRepositoryInterface $membershipPlanRepository,
        MembershipUserRepositoryInterface $membershipUserRepository,
        MembershipTransactionRepositoryInterface $membershipTransactionRepository,
    )
    {
        parent::__construct([
            'membershipPlan' => $membershipPlanRepository,
            'membershipUser' => $membershipUserRepository,
            'membershipTransaction' => $membershipTransactionRepository,
        ]);
    }

    public function getAllMembershipPlan()
    {
        return $this->getRepository('membershipPlan')->query()
            ->where('status', true)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function getMembershipPlanById($id)
    {
        return $this->getRepository('membershipPlan')->query()
            ->where('id', $id)
            ->where('status', true)
            ->first();
    }

    public function createMembershipForUser($userId, $membershipPlan, $dataTransfer): bool
    {
        $now = now();
        try {
            DB::beginTransaction();
            // Tạo membership cho người dùng
            $memberUser = $this->getRepository('membershipUser')->insertOne([
                'user_id' => $userId,
                'membership_plan_id' => $membershipPlan->id,
                'status' => CommonConstant::INACTIVE,
                'start_date' => $now,
                'end_date' => $now->copy()->addMonths($membershipPlan->duration),
            ]);

            // Tạo giao dịch membership
            $this->getRepository('membershipTransaction')->insertOne([
                'user_id' => $userId,
                'membership_user_id' => $memberUser->id,
                'money' => $dataTransfer['totalPrice'],
                'status' => MembershipTransactionStatus::WAITING,
                'transaction_code' => $dataTransfer['descBank'],
            ]);
            DB::commit();
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }
}

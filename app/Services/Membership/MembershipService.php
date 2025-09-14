<?php

namespace App\Services\Membership;

use App\Enums\CommonConstant;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Enums\PayTypes;
use App\Enums\Transactions\TransactionPaymentStatus;
use App\Enums\Transactions\TransactionPaymentType;
use App\Models\MembershipUser;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use App\Repositories\MembershipTransaction\MembershipTransactionRepositoryInterface;
use App\Repositories\MembershipUser\MembershipUserRepositoryInterface;
use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MembershipService extends BaseService implements MembershipServiceInterface
{
    public function __construct(
        MembershipPlanRepositoryInterface $membershipPlanRepository,
        MembershipUserRepositoryInterface $membershipUserRepository,
        MembershipTransactionRepositoryInterface $membershipTransactionRepository,
        TransactionPointRepositoryInterface $transactionPointRepository,
        TransactionPaymentRepositoryInterface $transactionPayment,
        UserRepository $userRepository
    ) {
        parent::__construct([
            'membershipPlan' => $membershipPlanRepository,
            'membershipUser' => $membershipUserRepository,
            'membershipTransaction' => $membershipTransactionRepository,
            'transactionPoint' => $transactionPointRepository,
            'transactionPayment' => $transactionPayment,
            'user' => $userRepository
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

            $memberUser = $this->getRepository('membershipUser')
                ->query()
                ->where('user_id', $userId)
                ->first();

            if (! $memberUser) {
                $memberUser = $this->getRepository('membershipUser')->insertOne([
                    'user_id' => $userId,
                    'membership_plan_id' => $membershipPlan->id,
                    'status' => CommonConstant::INACTIVE,
                    'start_date' => $now,
                    'end_date' => $now->copy()->addMonths($membershipPlan->duration),
                ]);

                if (! $memberUser || ! isset($memberUser->id)) {
                    throw new \Exception("Failed to create membership_user record");
                }
            }

            $this->getRepository('membershipTransaction')->insertOne([
                'user_id' => $userId,
                'membership_user_id' => $memberUser->id,
                'money' => $dataTransfer['totalPrice'] ?? 0,
                'status' => MembershipTransactionStatus::WAITING,
                'membership_plan_id' => $membershipPlan->id,
                'transaction_code' => $dataTransfer['descBank'] ?? null,
                'order_code' => $dataTransfer['orderCode'] ?? null,
                'expired_at' => now()->addMinutes(5),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Create membership error', [
                'msg' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function getMembershipTransactionByUserId($userId)
    {
        return $this->getRepository('membershipTransaction')->query()->where('user_id', $userId)->get();
    }


    public function payByPointsForMembershipUser($userId, $dataTransfer): bool
    {
        try {
            DB::beginTransaction();

            $memberUser = $this->getRepository('membershipUser')->query()->where('user_id', $userId)->where('status', CommonConstant::INACTIVE);

            $this->getRepository('membershipTransaction')->query()->where('order_code', $dataTransfer['orderCode'])->update([
                'user_id' => $userId,
                'membership_user_id' => $memberUser->id,
                'money' => $dataTransfer['points'],
                'status' => MembershipTransactionStatus::ACTIVE,
                'transaction_code' => 'PAY BY POINTS',
            ]);

            $memberUser->update(['status' => CommonConstant::ACTIVE]);
            $this->getRepository('membershipUser')->query()
                ->where('user_id', $memberUser->user_id)
                ->where('id', '!=', $memberUser->id)
                ->update(['status' => false]);
            $transactionPayment = $this->getRepository('transactionPayment')->insertOne([
                'user_id' => $userId,
                'type' => TransactionPaymentType::RECHANGE_POINT->value,
                'description' => 'PAY BY POINTS',
                'money' => $dataTransfer['points'],
                'status' => TransactionPaymentStatus::ACTIVE->value,
            ]);

            $this->getRepository('transactionPoint')->insertOne([
                'user_id' => $userId,
                'status' => TransactionPaymentStatus::ACTIVE->value,
                'point' => -$dataTransfer['points'],
                'transaction_payment_id' => $transactionPayment->id,
            ]);

            $this->getRepository('user')
                ->query()
                ->where('id', $userId)
                ->update([
                    'current_balance' => DB::raw("current_balance - {$dataTransfer['points']}")
                ]);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function reActivateMembershipForUser(MembershipUser $membershipUser): bool
    {
        try {
            DB::beginTransaction();
            $membershipUser->status = true;
            $membershipUser->save();
            // Cập nhật trạng thái của các MembershipUser khác cùng user
            $this->getRepository('membershipUser')->query()
                ->where('user_id', $membershipUser->user_id)
                ->where('id', '!=', $membershipUser->id)
                ->update(['status' => false]);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function validateActiveMembership(MembershipUser $item): bool
    {
        $now = now();
        return $item->status == CommonConstant::INACTIVE
            && $item->end_date >= $now
            && $item->membershipTransaction->where('status', MembershipTransactionStatus::ACTIVE->value)->isNotEmpty();
    }

    public function refreshMemberShipTransaction($userId, $dataTransfer): int
    {
        $membershipTransaction = $this->getRepository('membershipTransaction')
            ->query()
            ->where('order_code', $dataTransfer['orderCode'])
            ->where('user_id', $userId)
            ->first();

        if ($membershipTransaction) {
            return $membershipTransaction['status'];
        } else {
            return MembershipTransactionStatus::ACTIVE->value;
        }
    }
}

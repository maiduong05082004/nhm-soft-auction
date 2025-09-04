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
use Illuminate\Support\Facades\DB;

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

    public function createMembershipForUser($userId, $membershipPlan, $dataTransfer, $payType): bool
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


            if ($payType == PayTypes::POINTS->value) {
                $this->getRepository('membershipTransaction')->insertOne([
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
            } else {
                $this->getRepository('membershipTransaction')->insertOne([
                    'user_id' => $userId,
                    'membership_user_id' => $memberUser->id,
                    'money' => $dataTransfer['totalPrice'],
                    'status' => MembershipTransactionStatus::WAITING,
                    'transaction_code' => $dataTransfer['descBank'],
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function updateMembershipForUser($userId, $membershipPlan, $dataTransfer, $payType, $isUpgrade): bool
    {
        $now = now();

        try {
            DB::beginTransaction();
            $memberUser = $this->getRepository('membershipUser')
                ->query()
                ->where('user_id', $userId)
                ->first();

            if ($memberUser->end_date < now()) {
                $newEndDate = $now->copy()->addMonths($membershipPlan->duration);
            } else {
                $newEndDate = \Carbon\Carbon::parse($memberUser->end_date)->addMonths($membershipPlan->duration);
            }

            if ($isUpgrade) {
                $this->getRepository('membershipUser')->query()
                    ->where('user_id', $userId)
                    ->update([
                        'membership_plan_id' => $membershipPlan->id,
                        'end_date' => $newEndDate,
                    ]);
            }else {
                $this->getRepository('membershipUser')->query()
                    ->where('user_id', $userId)
                    ->update([
                        'end_date' => $newEndDate,
                    ]);
            }
            if ($payType == PayTypes::POINTS->value) {
                $this->getRepository('membershipTransaction')->insertOne([
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
            } else {
                $this->getRepository('membershipUser')->query()
                    ->where('user_id', $userId)
                    ->update([
                        'status' => CommonConstant::INACTIVE,
                    ]);
                $this->getRepository('membershipTransaction')->insertOne([
                    'user_id' => $userId,
                    'membership_user_id' => $memberUser->id,
                    'money' => $dataTransfer['totalPrice'],
                    'status' => MembershipTransactionStatus::WAITING,
                    'transaction_code' => $dataTransfer['descBank'],
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            return false;
        }
    }

    public function getMembershipTransactionByUserId($userId)
    {
        return $this->getRepository('membershipTransaction')->query()->where('user_id', $userId)->get();
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
}

<?php

namespace App\Services\Membership;

use App\Enums\CommonConstant;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Enums\PayTypes;
use App\Enums\Transactions\TransactionPaymentStatus;
use App\Enums\Transactions\TransactionPaymentType;
use App\Models\MembershipUser;
use App\Notifications\MembershipExpiredNotice;
use App\Notifications\MembershipExpiringSoonNotice;
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
                'expired_at' => now()->addMinutes(15),
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
            } else {
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


    public function remindMembershipExpiringSoon(int $daysBefore = 3): int
    {
        $targetDate = now()->addDays($daysBefore)->toDateString();

        $expiringSoon = $this->getRepository('membershipUser')->query()
            ->with(['membershipPlan', 'user']) // Eager load để tránh N+1
            ->where('status', CommonConstant::ACTIVE)
            ->whereNotNull('end_date')
            ->whereDate('end_date', $targetDate) // Sử dụng whereDate thay vì whereBetween
            ->get();

        $count = 0;
        foreach ($expiringSoon as $membershipUser) {
            try {
                // Kiểm tra xem đã gửi notification chưa (tránh duplicate)
                $alreadyNotified = $this->checkIfAlreadyNotified(
                    $membershipUser->user_id,
                    'MembershipExpiringSoonNotice',
                    $membershipUser->id
                );

                if ($alreadyNotified) {
                    continue;
                }

                $user = $membershipUser->user ?? $this->getRepository('user')->find($membershipUser->user_id);

                if ($user) {
                    $user(new MembershipExpiringSoonNotice($membershipUser, $daysBefore));
                    $count++;
                } else {
                    Log::warning("remindMembershipExpiringSoon: user not found for membership_user id {$membershipUser->id}");
                }
            } catch (\Exception $e) {
                Log::error('remindMembershipExpiringSoon error: ' . $e->getMessage(), [
                    'membership_user_id' => $membershipUser->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info("remindMembershipExpiringSoon: total reminders sent: {$count} for daysBefore={$daysBefore}");
        return $count;
    }

    public function checkMembershipExpired(): int
    {
        $now = now();

        $expired = $this->getRepository('membershipUser')->query()
            ->with(['membershipPlan', 'user'])
            ->where('status', CommonConstant::ACTIVE)
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now)
            ->get();

        $count = 0;

        foreach ($expired as $membershipUser) {
            DB::beginTransaction();
            try {
                // Cập nhật membership hiện tại
                $membershipUser->status = CommonConstant::INACTIVE;
                $membershipUser->save();
                // Kiểm tra duplicate notification
                $alreadyNotified = $this->checkIfAlreadyNotified(
                    $membershipUser->user_id,
                    'MembershipExpiredNotice',
                    $membershipUser->id
                );

                if (!$alreadyNotified) {
                    $user = $membershipUser->user ?? $this->getRepository('user')->find($membershipUser->user_id);

                    if ($user) {
                        $user->notify(new MembershipExpiredNotice($membershipUser));
                    } else {
                        Log::warning("checkMembershipExpired: user not found for membership_user id {$membershipUser->id}");
                    }
                }

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('checkMembershipExpired error: ' . $e->getMessage(), [
                    'membership_user_id' => $membershipUser->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info("checkMembershipExpired finished, total expired processed: {$count}");
        return $count;
    }

    private function checkIfAlreadyNotified(int $userId, string $notificationType, int $membershipUserId): bool
    {
        return DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Models\User')
            ->where('type', "App\Notifications\\{$notificationType}")
            ->whereJsonContains('data->membership_user_id', $membershipUserId)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
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

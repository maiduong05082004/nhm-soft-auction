<?php

namespace App\Services\Transaction;

use App\Enums\CommonConstant;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Exceptions\ServiceException;
use App\Models\MembershipTransaction;
use App\Repositories\MembershipTransaction\MembershipTransactionRepositoryInterface;
use App\Repositories\MembershipUser\MembershipUserRepositoryInterface;
use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseService implements TransactionServiceInterface
{
    public function __construct(
        TransactionPaymentRepositoryInterface    $transactionPaymentRepository,
        TransactionPointRepositoryInterface      $transactionPointRepository,
        MembershipTransactionRepositoryInterface $membershipTransactionRepository,
        MembershipUserRepositoryInterface $membershipUserRepository,
        UserRepositoryInterface $useRepository
    ) {
        parent::__construct([
            'transactionPaymentRepository' => $transactionPaymentRepository,
            'transactionPointRepository' => $transactionPointRepository,
            'membershipTransactionRepository' => $membershipTransactionRepository,
            'membershipUserRepository' => $membershipUserRepository,
            'userRepository' => $useRepository
        ]);
    }

    public function getQueryTransactionMembershipAdmin()
    {
        return $this->getRepository('membershipTransactionRepository')
            ->query()->with(['user', 'membershipUser', 'membershipUser.membershipPlan']);
    }

    public function confirmMembershipTransaction(MembershipTransaction $membershipTransaction, MembershipTransactionStatus $status)
    {
        $now = now();
        try {
            DB::beginTransaction();
            if (in_array($membershipTransaction->status, [MembershipTransactionStatus::WAITING->value, MembershipTransactionStatus::FAILED->value])) {
                switch ($status) {
                    case MembershipTransactionStatus::ACTIVE:
                        $membershipTransaction->status = MembershipTransactionStatus::ACTIVE->value;
                        $plan = $membershipTransaction->membershipPlan;
                        $membershipTransaction->save();
                        $membershipUser = $membershipTransaction->membershipUser;
                        if ($membershipUser->status == false) {
                            $membershipUser->status = true;

                            $membershipUser->start_date = $now;
                            $membershipUser->end_date   = $now->copy()->addMonths($plan->duration);
                        } else {

                            $endDate = $membershipUser->end_date ? \Carbon\Carbon::parse($membershipUser->end_date) : null;

                            if ($endDate && $endDate->greaterThan($now)) {
                                $membershipUser->end_date = $endDate->copy()->addMonths($plan->duration);
                            } else {
                                $membershipUser->start_date = $now;
                                $membershipUser->end_date   = $now->copy()->addMonths($plan->duration);
                            }
                        }
                        if ($membershipUser->membership_plan_id != $plan->id) {
                            $membershipUser->membership_plan_id = $plan->id;
                        }

                        $membershipUser->save();
                        $this->getRepository('membershipUserRepository')->query()
                            ->where('user_id', $membershipUser->user_id)
                            ->where('id', '!=', $membershipUser->id)
                            ->update(['status' => false]);
                        // Cập nhật trạng thái user
                        $this->getRepository('userRepository')->query()
                            ->where('id', $membershipUser->user_id)
                            ->update(['membership' =>  1]);

                        DB::commit();

                        return true;
                    case MembershipTransactionStatus::FAILED:
                        // Đổi trạng thái giao dịch
                        $membershipTransaction->status = MembershipTransactionStatus::FAILED->value;
                        $membershipTransaction->save();
                        DB::commit();
                        return true;
                    default:
                        break;
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
        }
        return false;
    }

    public function confirmMembershipTransactionForwebhook($orderCode, $status)
    {
        $now  = now();
        try {
            DB::beginTransaction();
            $membershipTransaction = $this->getRepository('membershipTransactionRepository')->query()->where('order_code', $orderCode)->first();

            if ($membershipTransaction) {
                if ($status == MembershipTransactionStatus::ACTIVE->value) {
                    $membershipTransaction->status = MembershipTransactionStatus::ACTIVE->value;
                    $plan = $membershipTransaction->membershipPlan;
                    $membershipTransaction->save();
                    $membershipUser = $membershipTransaction->membershipUser;
                    if ($membershipUser->status == false) {
                        $membershipUser->status = true;

                        $membershipUser->start_date = $now;
                        $membershipUser->end_date   = $now->copy()->addMonths($plan->duration);
                    } else {

                        $endDate = $membershipUser->end_date ? \Carbon\Carbon::parse($membershipUser->end_date) : null;

                        if ($endDate && $endDate->greaterThan($now)) {
                            $membershipUser->end_date = $endDate->copy()->addMonths($plan->duration);
                        } else {
                            $membershipUser->start_date = $now;
                            $membershipUser->end_date   = $now->copy()->addMonths($plan->duration);
                        }
                    }
                    if ($membershipUser->membership_plan_id != $plan->id) {
                        $membershipUser->membership_plan_id = $plan->id;
                    }

                    $membershipUser->save();
                    $this->getRepository('membershipUserRepository')->query()
                        ->where('user_id', $membershipUser->user_id)
                        ->where('id', '!=', $membershipUser->id)
                        ->update(['status' => false]);
                    // Cập nhật trạng thái user
                    $this->getRepository('userRepository')->query()
                        ->where('id', $membershipUser->user_id)
                        ->update(['membership' =>  1]);
                } else if ($membershipTransaction && $status == MembershipTransactionStatus::WAITING->value) {
                    $membershipTransaction->status = MembershipTransactionStatus::WAITING->value;
                    $membershipTransaction->save();

                    $membershipUser = $membershipTransaction->membershipUser;
                    $membershipUser->status = false;
                    $membershipUser->save();
                } else {
                    $membershipTransaction->status = MembershipTransactionStatus::FAILED->value;
                    $membershipTransaction->save();

                    $membershipUser = $membershipTransaction->membershipUser;
                    $membershipUser->status = false;
                    $membershipUser->save();
                }
            }
            DB::commit();
        } catch (ServiceException $e) {
            DB::rollBack();
        }
    }
}

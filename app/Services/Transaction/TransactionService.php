<?php

namespace App\Services\Transaction;

use App\Enums\CommonConstant;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Models\MembershipTransaction;
use App\Repositories\MembershipTransaction\MembershipTransactionRepositoryInterface;
use App\Repositories\MembershipUser\MembershipUserRepositoryInterface;
use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseService implements TransactionServiceInterface
{
    public function __construct(
        TransactionPaymentRepositoryInterface    $transactionPaymentRepository,
        TransactionPointRepositoryInterface      $transactionPointRepository,
        MembershipTransactionRepositoryInterface $membershipTransactionRepository,
        MembershipUserRepositoryInterface $membershipUserRepository
    )
    {
        parent::__construct([
            'transactionPaymentRepository' => $transactionPaymentRepository,
            'transactionPointRepository' => $transactionPointRepository,
            'membershipTransactionRepository' => $membershipTransactionRepository,
            'membershipUserRepository' => $membershipUserRepository,
        ]);
    }

    public function getQueryTransactionMembershipAdmin()
    {
        return $this->getRepository('membershipTransactionRepository')
            ->query()->with(['user', 'membershipUser', 'membershipUser.membershipPlan']);
    }

    public function confirmMembershipTransaction(MembershipTransaction $membershipTransaction, MembershipTransactionStatus $status)
    {
        try {
            DB::beginTransaction();
            if (in_array($membershipTransaction->status, [MembershipTransactionStatus::WAITING->value, MembershipTransactionStatus::FAILED->value])) {
                switch ($status) {
                    case MembershipTransactionStatus::ACTIVE:
                        // Đổi trạng thái giao dịch
                        $membershipTransaction->status = MembershipTransactionStatus::ACTIVE->value;
                        $membershipTransaction->save();

                        // Cập nhật trạng thái của MembershipUser
                        $membershipUser = $membershipTransaction->membershipUser;
                        $membershipUser->status = true;
                        $membershipUser->save();

                        // Cập nhật trạng thái của các MembershipUser khác cùng user
                        $this->getRepository('membershipUserRepository')->query()
                            ->where('user_id', $membershipUser->user_id)
                            ->where('id', '!=', $membershipUser->id)
                            ->update(['status' => false]);
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
}

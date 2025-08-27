<?php

namespace App\Services\PointPackages;

use App\Enums\Transactions\TransactionPaymentStatus;
use App\Enums\Transactions\TransactionPaymentType;
use App\Models\TransactionPayment;
use App\Repositories\PointPackage\PointPackageRepository;
use App\Repositories\TransactionPayment\TransactionPaymentRepository;
use App\Repositories\TransactionPoint\TransactionPointRepository;
use App\Repositories\Users\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class PointPackageService extends BaseService implements PointPackageServiceInterface
{
    protected $wishlistRepository;

    public function __construct(
        PointPackageRepository $pointPackageRepository,
        TransactionPaymentRepository $transactionPayment,
        TransactionPointRepository $transactionPoint,
        UserRepository $userRepository
    ) {
        parent::__construct([
            'pointPackage' => $pointPackageRepository,
            'transactionPayment' => $transactionPayment,
            'transactionPoint' => $transactionPoint,
            'user' => $userRepository
        ]);
    }

    public function getAllPointPackage()
    {
        return  $this->getRepository('pointPackage')->query()->where('status', true)->get();
    }

    public function getPointPackageById($packageId)
    {
        return  $this->getRepository('pointPackage')->query()->where('id', $packageId)->where('status', true)->first();
    }

    public function createTransactionPayment($dataTransfer, $userId)
    {
        try {
            DB::beginTransaction();
            $transactionPayment = $this->getRepository('transactionPayment')->insertOne([
                'user_id' => $userId,
                'type' => TransactionPaymentType::RECHANGE_POINT->value,
                'description' => $dataTransfer['descBank'],
                'money' => $dataTransfer['totalPrice'],
                'status' => TransactionPaymentStatus::WAITING,
            ]);

            $this->getRepository('transactionPoint')->insertOne([
                'user_id' => $userId,
                'status' => TransactionPaymentStatus::WAITING,
                'point' => $dataTransfer['points'],
                'transaction_payment_id' => $transactionPayment->id,
            ]);
            DB::commit();
            return true;
        } catch (\App\Exceptions\ServiceException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getTransactionPaymentByType($type)
    {
        return $this->getRepository('transactionPayment')->query()->with('transactionPoint')->where('type', $type)->orderBy('created_at');
    }

    public function confirmPointTransaction(TransactionPayment $record, TransactionPaymentStatus $status)
    {
        try {
            DB::beginTransaction();

            if (in_array($record->status, [TransactionPaymentStatus::WAITING->value, TransactionPaymentStatus::FAILED->value])) {
                switch ($status) {
                    case TransactionPaymentStatus::ACTIVE:
                        // Đổi trạng thái giao dịch
                        $record->status = TransactionPaymentStatus::ACTIVE->value;
                        $record->save();
                        // Lấy transaction point liên quan
                        $transactionPoint = $this->getRepository('transactionPoint')
                            ->query()
                            ->where('transaction_payment_id', $record->id)
                            ->first();

                        if ($transactionPoint) {
                            $transactionPoint->update(['status' => TransactionPaymentStatus::ACTIVE]);
                            $points = $transactionPoint->point;

                            // Cập nhật trạng thái user
                            $this->getRepository('user')
                                ->query()
                                ->where('id', $record->user_id)
                                ->update([
                                    'current_balance' => DB::raw("current_balance + {$points}")
                                ]);
                        }

                        DB::commit();
                        return true;

                    case TransactionPaymentStatus::FAILED:
                        $record->status = TransactionPaymentStatus::FAILED->value;
                        $record->save();
                        DB::commit();
                        return true;

                    default:
                        DB::rollBack();
                        return false;
                }
            }

            DB::rollBack();
            return false;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}

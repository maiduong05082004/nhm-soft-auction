<?php

namespace App\Repositories\TransactionPayment;

use App\Enums\Transactions\TransactionPaymentType;
use App\Models\TransactionPayment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class TransactionPaymentRepository extends BaseRepository implements TransactionPaymentRepositoryInterface
{
    public function getModel(): string
    {
       return TransactionPayment::class;
    }
    public function sumTransTypeByUserId(TransactionPaymentType $type, $userId)
    {
        // Sử dụng cache để lưu trữ kết quả tổng tiền theo userId và type
        // Thời gian lưu cache là 10 phút để giảm tải truy vấn cơ sở dữ liệu
        $totalPayment = Cache::remember("total_payment_user_{$userId}_type_{$type->value}", now()->addMinutes(10), function () use ($userId, $type) {
            return $this->getQueryBuilder()
                ->where('user_id', $userId)
                ->where('type', $type->value)
                ->sum('money');
        });
        return $totalPayment;
    }

}

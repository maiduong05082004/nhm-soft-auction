<?php

namespace App\Repositories\TransactionPoint;

use App\Enums\Transactions\TransactionPaymentStatus;
use App\Models\TransactionPoint;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class TransactionPointRepository extends BaseRepository implements TransactionPointRepositoryInterface
{
    public function getModel(): string
    {
       return TransactionPoint::class;
    }
    public function sumTransByUserId($userId)
    {
        // Sử dụng cache để lưu trữ kết quả tổng point theo userId
        // Thời gian lưu cache là 10 phút để giảm tải truy vấn cơ sở dữ liệu
        // $totalPayment = Cache::remember("total_point_user_{$userId}}", now()->addMinutes(10), function () use ($userId) {
            return $this->getQueryBuilder()
                ->where('user_id', $userId)
                ->where('status', TransactionPaymentStatus::ACTIVE)
                ->sum('point');
        // });
        // return $totalPayment;
    }
}

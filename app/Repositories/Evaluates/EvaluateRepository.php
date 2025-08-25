<?php

namespace App\Repositories\Evaluates;

use App\Models\Evaluate;
use App\Repositories\BaseRepository;

class EvaluateRepository extends BaseRepository implements EvaluateRepositoryInterface
{
	public function getModel(): string
	{
		return Evaluate::class;
	}

    public function getEvaluatesByProduct($productId, $status = 'active')
    {
        return $this->model->where('product_id', $productId)
            ->where('status', $status)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAverageRating($productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('status', 'active')
            ->avg('star_rating');
    }

    public function getRatingDistribution($productId)
    {
        $rows = $this->model->selectRaw('star_rating, COUNT(*) as total')
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->groupBy('star_rating')
            ->pluck('total', 'star_rating');

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = (int) ($rows[$i] ?? 0);
        }
        return $distribution;
    }

    public function getTotalReviews($productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('status', 'active')
            ->count();
    }

    public function getUserAverageSellerRating(int $userId)
    {
        return $this->model->where('status', 'active')
            ->where('user_id', $userId)
            ->avg('seller_rating');
    }

    public function getUserTotalSellerReviews(int $userId)
    {
        return $this->model->where('status', 'active')
            ->where('user_id', $userId)
            ->count();
    }

    public function getUserSellerRatingDistribution(int $userId)
    {
        $rows = $this->model->selectRaw('seller_rating, COUNT(*) as total')
            ->where('status', 'active')
            ->where('user_id', $userId)
            ->groupBy('seller_rating')
            ->pluck('total', 'seller_rating');

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = (int) ($rows[$i] ?? 0);
        }
        return $distribution;
    }
}

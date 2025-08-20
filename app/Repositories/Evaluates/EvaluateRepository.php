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
        $evaluates = $this->model->where('product_id', $productId)
            ->where('status', 'active')
            ->get();

        return [
            5 => $evaluates->where('star_rating', 5)->count(),
            4 => $evaluates->where('star_rating', 4)->count(),
            3 => $evaluates->where('star_rating', 3)->count(),
            2 => $evaluates->where('star_rating', 2)->count(),
            1 => $evaluates->where('star_rating', 1)->count(),
        ];
    }

    public function getTotalReviews($productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('status', 'active')
            ->count();
    }
}

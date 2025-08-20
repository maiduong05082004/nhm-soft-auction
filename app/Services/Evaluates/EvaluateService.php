<?php

namespace App\Services\Evaluates;

use App\Services\BaseService;
use App\Repositories\Evaluates\EvaluateRepository;

class EvaluateService extends BaseService implements EvaluateServiceInterface
{
    public function __construct(EvaluateRepository $evaluateRepo)
    {
        parent::__construct([
            'evaluate' => $evaluateRepo,
        ]);
    }

    public function getProductEvaluates($productId)
    {
        return $this->repositories['evaluate']->getEvaluatesByProduct($productId);
    }

    public function getProductRatingStats($productId)
    {
        $evaluates = $this->repositories['evaluate']->getEvaluatesByProduct($productId);
        $totalReviews = $this->repositories['evaluate']->getTotalReviews($productId);
        $averageRating = $this->repositories['evaluate']->getAverageRating($productId);
        $ratingDistribution = $this->repositories['evaluate']->getRatingDistribution($productId);

        return [
            'evaluates' => $evaluates,
            'totalReviews' => $totalReviews,
            'averageRating' => $totalReviews > 0 ? round($averageRating, 1) : 0,
            'ratingDistribution' => $ratingDistribution,
        ];
    }
}

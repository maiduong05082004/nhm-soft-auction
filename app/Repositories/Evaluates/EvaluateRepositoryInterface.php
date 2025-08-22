<?php

namespace App\Repositories\Evaluates;

use App\Repositories\BaseRepositoryInterface;

interface EvaluateRepositoryInterface extends BaseRepositoryInterface
{
    public function getEvaluatesByProduct($productId, $status = 'active');
    public function getAverageRating($productId);
    public function getRatingDistribution($productId);
    public function getTotalReviews($productId);
    public function getUserAverageSellerRating(int $userId);
    public function getUserTotalSellerReviews(int $userId);
    public function getUserSellerRatingDistribution(int $userId);
}

<?php

namespace App\Services\Evaluates;

use App\Services\BaseServiceInterface;

interface EvaluateServiceInterface extends BaseServiceInterface
{
    public function getProductEvaluates($productId);
    public function getProductRatingStats($productId);
    public function getUserSellerRatingStats(int $userId);
}

<?php

namespace App\Repositories\Products;

use App\Repositories\BaseRepository;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getModel(): Model
    {
        return new Product();
    }
}
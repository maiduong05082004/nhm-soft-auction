<?php

namespace App\Repositories\Products;

use App\Enums\Product\ProductTypeSale;
use App\Repositories\BaseRepository;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel(): string
    {
        return Product::class;
    }

    public function getProductByFilter($query = [], $page = 1, $perPage = 12): LengthAwarePaginator
    {
        $builder = $this->model
            ->with(['category', 'auction', 'firstImage'])
            ->where('status', 1);

        if (!empty($query['name'])) {
            $builder->where('name', 'like', '%' . $query['name'] . '%');
        }

        if (!empty($query['type'])) {
            $typeMap = [
                'sale' => ProductTypeSale::SALE,
                'auction' => ProductTypeSale::AUCTION
            ];
            $typeValue = isset($typeMap[$query['type']]) ? $typeMap[$query['type']] : $query['type'];
            $builder->where('type_sale', $typeValue);
        }

        if (!empty($query['min_price'])) {
            $builder->where('price', '>=', $query['min_price']);
        }
        if (!empty($query['max_price'])) {
            $builder->where('price', '<=', $query['max_price']);
        }

        if (!empty($query['categoryId'])) {
            $builder->where('category_id', $query['categoryId']);
        }

        if (!empty($query['state'])) {
            $builder->where('state', $query['state']);
        }

        if (!empty($query['is_hot'])) {
            $builder->where('is_hot', 1);
        }

        if (!empty($query['is_new'])) {
            $builder->where('created_at', '<=', now()->subWeek());
        }
        if (!empty($query['orderBy'])) {
            switch ($query['orderBy']) {
                case 'price_asc':
                    $builder->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $builder->orderBy('price', 'desc');
                    break;
                case 'views_desc':
                    $builder->orderBy('view', 'desc');
                    break;
                case 'name_asc':
                    $builder->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $builder->orderBy('name', 'desc');
                    break;
                case 'created_at_asc':
                    $builder->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $builder->orderBy('created_at', 'desc');
                    break;
                case 'view_asc':
                    $builder->orderby('view', 'asc');
                case 'view_desc':
                    $builder->orderby('view', 'desc');
                    break;
                default:
                    $builder->orderBy('created_at', 'desc');
            }
        } else {
            $builder->orderBy('created_at', 'desc');
        }
        return $builder->paginate($perPage, ['*'], 'page', $page);
    }

    public function incrementViewCount($productId)
    {
        return $this->model->find($productId)->increment('view');
    }
}

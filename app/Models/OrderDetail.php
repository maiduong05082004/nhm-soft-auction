<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'quantity',
        'total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'total' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });

        static::saving(function (OrderDetail $model) {
            $price = 0.0;
            if ($model->relationLoaded('product') && $model->product) {
                $price = (float) $model->product->price;
            } else if (! empty($model->product_id)) {
                $price = (float) (Product::find($model->product_id)?->price ?? 0);
            }
            $quantity = (float) ($model->quantity ?? 0);
            $model->total = $quantity * $price;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

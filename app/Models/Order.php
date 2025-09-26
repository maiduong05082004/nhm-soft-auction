<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_detail_id',
        'product_id',
        'quantity',
        'total',
        'payment_id'
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

        static::saving(function (Order $model) {
            if (is_null($model->total)) {
                $price = 0.0;
                if (! empty($model->product_id)) {
                    $price = (float) (Product::find($model->product_id)?->price ?? 0);
                }
                $quantity = (float) ($model->quantity ?? 0);
                $model->total = $quantity * $price;
            }
        });
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

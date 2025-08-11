<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'code_orders',
        'user_id',
        'email_receiver',
        'ship_address',
        'payment_method',
        'shipping_fee',
        'subtotal',
        'total',
        'note',
        'canceled_reason',
        'canceled_at',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}

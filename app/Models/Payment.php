<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_detail_id',
        'user_id',
        'payment_method',
        'amount',
        'transaction_id',
        'pay_date',
        'currency_code',
        'payer_email',
        'transaction_fee',
        'status',
        'confirmation_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    } 
}

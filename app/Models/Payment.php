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
        'order_id',
        'user_id',
        'payment_method',
        'amount',
        'transaction_id',
        'pay_date',
        'currency_code',
        'payer_id',
        'payer_email',
        'transaction_fee',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

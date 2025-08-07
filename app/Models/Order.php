<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

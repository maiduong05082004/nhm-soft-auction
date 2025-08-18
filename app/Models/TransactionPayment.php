<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;

    protected $table = 'transaction_payment';

    protected $fillable = [
        'id',
        'money',
        'type',
        'description',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    // Relation với model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

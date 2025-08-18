<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPoint extends Model
{
    use HasFactory;

    protected $table = 'transaction_point';

    protected $fillable = [
        'id',
        'point',
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

<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'card_number',
        'user_id',
        'bin_bank',
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
}

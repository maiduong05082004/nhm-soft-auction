<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Evaluate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'evaluate';
    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'order_detail_id',
        'star_rating',
        'comment',
        'status',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

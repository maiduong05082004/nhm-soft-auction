<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }
}

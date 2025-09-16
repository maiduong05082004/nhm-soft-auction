<?php

namespace App\Models;

use App\Enums\Config\ConfigName;
use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }
    protected $fillable = [
        'id',
        'config_key',
        'config_value',
        'description',
    ];

    public static function getValue(ConfigName $key, $default = null)
    {
        return optional(
            static::where('config_key', $key)->first()
        )->config_value ?? $default;
    }
}

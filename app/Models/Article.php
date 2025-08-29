<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'seo' => 'array'
    ];

    protected $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'image',
        'view',
        'user_id',
        'sort',
        'status',
        'category_article_id',
        'publish_time',
        'seo'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryArticle::class, 'category_article_id');
    }
}

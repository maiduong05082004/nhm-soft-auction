<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'seo' => 'array'
    ];

    protected $fillable = [
        'id',
        'name',
        'slug',
        'price',
        'description',
        'view',
        'stock',
        'min_bid_amount',
        'max_bid_amount',
        'type_sale',
        'category_id',
        'start_time',
        'end_time',
        'status',
        'is_hot',
        'created_by',
        'seo',
        'pay_method',
        'state',
        'brand'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluate::class);
    }

    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->latest();
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('position', 0);
    }

    public function getMainImage()
    {
        $mainImage = $this->mainImage()->first();
        return $mainImage ? $mainImage->image_url : null;
    }

    public function getRouteKey(): mixed
    {
        return $this->slug;
    }

}

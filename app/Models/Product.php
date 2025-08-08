<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
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
    ];

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
        // dd($this->hasOne(ProductImage::class)->latest());
        return $this->hasOne(ProductImage::class, 'product_id')->latest(); // or ->oldest()
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductImage extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'product_id',
        'image_url',
        'position',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

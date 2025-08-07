<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuctionBid extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'auction_id',
        'user_id',
        'bid_price',
        'bid_time',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

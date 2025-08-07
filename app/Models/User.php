<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_balance',
        'reputation',
        'membership',
        'phone',
        'address',
        'avatar',
        'introduce',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'membership' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluate::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function auctionBids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at', 'desc');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function wonAuctions()
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }

    public function getDynamicCurrentBalanceAttribute()
    {
        $balance = $this->transactions()->sum('point_change');
        return $balance < 0 ? 0 : $balance;
    }

    public function getTransactionCountAttribute()
    {
        return $this->transactions()->count();
    }

    public function getTotalRechargeAttribute()
    {
        return $this->transactions()->where('type_transaction', 'recharge_point')->sum('point_change');
    }

    public function getTotalBidAttribute()
    {
        return $this->transactions()->where('type_transaction', 'bid')->sum('point_change');
    }

    public function getTotalBuyProductAttribute()
    {
        return $this->transactions()->where('type_transaction', 'buy_product')->sum('point_change');
    }

}

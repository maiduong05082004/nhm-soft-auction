<?php

namespace App\Models;

use Filament\Panel;
use App\Utils\HelperFunc;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\CommonConstant;
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * Phải xác thực email mới được truy cập vào admin
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
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
        'contact_info'
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
        'contact_info' => 'array'
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function membershipUsers()
    {
        return $this->hasMany(MembershipUser::class);
    }

    public function membershipPlans()
    {
        return $this->belongsToMany(MembershipPlan::class, 'membership_users')
            ->withPivot(['start_date', 'end_date', 'status'])
            ->withTimestamps();
    }

    /**
     * Lấy gói membership đang active
     */
    public function activeMemberships()
    {
        return $this->membershipPlans()->wherePivot('status', CommonConstant::ACTIVE);
    }

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

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Các mối quan hệ với TransactionPayment
    public function transactionPayments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    // Các mối quan hệ với TransactionPoint
    public function transactionPoints()
    {
        return $this->hasMany(TransactionPoint::class);
    }

    public function wonAuctions()
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }

    public function creditCards()
    {
        return $this->hasMany(CreditCard::class);
    }

}

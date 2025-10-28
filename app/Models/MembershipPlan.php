<?php

namespace App\Models;


use App\Enums\ConfigMembership;
use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class MembershipPlan extends Model
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
        'id',
        'name',
        'description',
        'price',
        'duration',
        'config',
        'status',
        'badge',
        'sort',
        'badge_color',
        'is_testing'
    ];

    protected $casts = [
        'config' => 'array',
        'status' => 'boolean',
        'is_testing' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    public function membershipUsers()
    {
        return $this->hasMany(MembershipUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'membership_users')
            ->withPivot(['start_date', 'end_date', 'status'])
            ->withTimestamps();
    }

    public function getConfig(ConfigMembership $key, $default = null)
    {
        return $this->config[$key->value] ?? $default;
    }

    public function hasFeature(ConfigMembership $key): bool
    {
        return (bool) $this->getConfig($key, false);
    }

    public function getDiscountPercentage(): int
    {
        return $this->getConfig(ConfigMembership::DISCOUNT_PERCENTAGE, 0);
    }

    public function canListProductsFree(): bool
    {
        return $this->hasFeature(ConfigMembership::FREE_PRODUCT_LISTING);
    }
    public function canParticipateAuctionsFree(): bool
    {
        return $this->hasFeature(ConfigMembership::FREE_AUCTION_PARTICIPATION);
    }

    public function getMaxProductsPerMonth(): int
    {
        return $this->getConfig(ConfigMembership::MAX_PRODUCTS_PER_MONTH, 0);
    }
}

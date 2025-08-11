<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

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
    ];

    protected $casts = [
        'config' => 'array',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
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

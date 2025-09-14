<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class MembershipTransaction extends Model
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
        'user_id',
        'membership_user_id',
        'money',
        'transaction_code',
        'status',
        'order_code',
        'membership_plan_id',
        'expired_at'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function membershipUser()
    {
        return $this->belongsTo(MembershipUser::class, 'membership_user_id');
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id', 'id');
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;
use Osiset\ShopifyApp\Traits\ShopModel;

class User extends Authenticatable implements IShopModel
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use ShopModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_extension_enabled',
        'extension_enabled_at',
        'segment_events',
        'main_theme_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'segment_events' => 'array',
        'is_extension_enabled' => 'boolean',
    ];

    public function subscribedPlan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function activeCharge()
    {
        return $this->hasOne(Charge::class, 'user_id', 'id')->where('status', 'ACTIVE');
    }

    public function reviews()
    {
        return $this->hasOne(Review::class, 'user_id', 'id');
    }
}

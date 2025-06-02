<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'photo',
        'nif',
        'default_delivery_address',
        'default_payment_type',
        'default_payment_reference',
        'type',
        'blocked',
        'custom',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked' => 'boolean',
            'custom' => 'array',
        ];
    }

    public function getProfileImageUrlAttribute(): string
    {
        $path = 'users/' . $this->photo;

        if ($this->photo && file_exists(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset('storage/users/anonymous.png');
    }

    public function getWishlist(): array
    {
        return $this->custom['wishlist'] ?? [];
    }

    public function setWishlist(array $wishlist): void
    {
        $custom = $this->custom ?? [];
        $custom['wishlist'] = $wishlist;
        $this->custom = $custom;
        $this->save();
    }


    // ------------------- CODIGO RELACOES ----------------------
    public function card(): HasOne
    {
        return $this->hasOne(Card::class, 'id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'member_id');
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'registered_by_user_id');
    }


    public function supplyOrders(): HasMany
    {
        return $this->hasMany(SupplyOrder::class, 'registered_by_user_id');
    }
}

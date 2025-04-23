<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function resellerProfile()
    {
        return $this->hasOne(ResellerProfile::class);
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isReseller()
    {
        return $this->role === 'reseller';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
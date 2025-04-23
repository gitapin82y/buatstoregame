<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_description',
        'store_logo',
        'store_banner',
        'store_theme_color',
        'membership_level',
        'membership_expires_at',
        'custom_domain',
        'subdomain',
        'balance',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_tiktok',
    ];

    protected $casts = [
        'membership_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipTransactions()
    {
        return $this->hasMany(MembershipTransaction::class, 'reseller_id');
    }

    public function resellerGames()
    {
        return $this->hasMany(ResellerGame::class, 'reseller_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'reseller_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'reseller_id');
    }

    public function analytics()
    {
        return $this->hasMany(Analytic::class, 'reseller_id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'reseller_id');
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class, 'reseller_id');
    }

    public function isActive()
    {
        return now()->lt($this->membership_expires_at);
    }

    public function isGracePeriod()
    {
        return now()->gt($this->membership_expires_at) && 
               now()->lt($this->membership_expires_at->addDays(7));
    }

    public function getDomainUrl()
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        } elseif ($this->subdomain) {
            return 'https://' . $this->subdomain . '.buattokogame.com';
        }
        return null;
    }
}


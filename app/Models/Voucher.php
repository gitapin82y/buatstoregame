<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'code',
        'discount_type',
        'discount_amount',
        'min_purchase',
        'max_discount',
        'starts_at',
        'expires_at',
        'max_usage',
        'used_count',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isValid()
    {
        return $this->isActive() && 
               now()->gte($this->starts_at) && 
               now()->lte($this->expires_at) && 
               ($this->max_usage === null || $this->used_count < $this->max_usage);
    }
}

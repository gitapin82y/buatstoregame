<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'package_id',
        'invoice_number',
        'amount',
        'payment_method',
        'payment_status',
        'xendit_invoice_id',
        'payment_link',
        'expired_at',
        'paid_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }

    public function package()
    {
        return $this->belongsTo(MembershipPackage::class, 'package_id');
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'transaction_id',
        'commission_amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function transaction()
    {
        return $this->belongsTo(UserTransaction::class, 'transaction_id');
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }
}

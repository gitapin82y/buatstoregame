<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reseller_id',
        'game_id',
        'service_id',
        'option_id',
        'invoice_number',
        'user_identifier',
        'server_identifier',
        'amount',
        'payment_method',
        'payment_status',
        'process_status',
        'xendit_invoice_id',
        'payment_link',
        'expired_at',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function service()
    {
        return $this->belongsTo(GameService::class, 'service_id');
    }

    public function option()
    {
        return $this->belongsTo(ServiceOption::class, 'option_id');
    }

    public function affiliateTransaction()
    {
        return $this->hasOne(AffiliateTransaction::class, 'transaction_id');
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted()
    {
        return $this->process_status === 'completed';
    }
}
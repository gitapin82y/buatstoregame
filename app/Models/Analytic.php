<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'date',
        'total_sales',
        'total_transactions',
        'total_profit',
        'popular_game',
        'popular_service',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerServiceOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_game_service_id',
        'service_option_id',
        'selling_price',
        'is_active',
    ];

    public function resellerGameService()
    {
        return $this->belongsTo(ResellerGameService::class);
    }

    public function serviceOption()
    {
        return $this->belongsTo(ServiceOption::class);
    }
}
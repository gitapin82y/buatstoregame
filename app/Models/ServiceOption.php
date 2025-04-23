<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_service_id',
        'name',
        'description',
        'api_code',
        'base_price',
        'status',
    ];

    public function gameService()
    {
        return $this->belongsTo(GameService::class);
    }

    public function resellerServiceOptions()
    {
        return $this->hasMany(ResellerServiceOption::class);
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class, 'option_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
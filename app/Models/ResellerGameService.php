<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerGameService extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_game_id',
        'game_service_id',
        'price',
        'profit_margin',
        'is_active',
        'display_order',
    ];

    public function resellerGame()
    {
        return $this->belongsTo(ResellerGame::class);
    }

    public function gameService()
    {
        return $this->belongsTo(GameService::class);
    }

    public function resellerServiceOptions()
    {
        return $this->hasMany(ResellerServiceOption::class);
    }
}
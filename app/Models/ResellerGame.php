<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'game_id',
        'is_active',
        'profit_margin',
        'display_order',
    ];

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function resellerGameServices()
    {
        return $this->hasMany(ResellerGameService::class);
    }
}
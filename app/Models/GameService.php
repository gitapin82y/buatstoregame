<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameService extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'slug',
        'type',
        'description',
        'image',
        'price_range',
        'status',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function options()
    {
        return $this->hasMany(ServiceOption::class);
    }

    public function resellerGameServices()
    {
        return $this->hasMany(ResellerGameService::class);
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class, 'service_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
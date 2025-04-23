<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'status',
    ];

    public function services()
    {
        return $this->hasMany(GameService::class);
    }

    public function resellerGames()
    {
        return $this->hasMany(ResellerGame::class);
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}

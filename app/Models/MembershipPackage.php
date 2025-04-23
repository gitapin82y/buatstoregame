<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'price',
        'duration_days',
        'description',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(MembershipTransaction::class, 'package_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}

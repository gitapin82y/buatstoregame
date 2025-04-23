<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'status',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_url',
        'api_key',
        'api_secret',
        'status',
    ];

    public function isActive()
    {
        return $this->status === 'active';
    }
}
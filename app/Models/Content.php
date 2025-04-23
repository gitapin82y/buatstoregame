<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'title',
        'content',
        'image',
        'type',
        'platform',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(ResellerProfile::class, 'reseller_id');
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }
}

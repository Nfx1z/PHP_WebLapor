<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'rtsp_url',
        'camera_index',
        'is_active',
        'status',
        'last_seen',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'online' => 'success',
            'offline' => 'secondary',
            'error' => 'danger',
            default => 'secondary',
        };
    }
}
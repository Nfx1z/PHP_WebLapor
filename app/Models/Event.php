<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'camera_id',
        'event_type',
        'persons_count',
        'details',
        'image_path',
        'telegram_sent',
        'detected_at',
    ];

    protected $casts = [
        'telegram_sent' => 'boolean',
        'detected_at' => 'datetime',
    ];

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    public function getEventColorAttribute()
    {
        return match($this->event_type) {
            'LAPOR' => 'danger',
            'KHALWAT' => 'warning',
            'IKHTILAT' => 'info',
            default => 'secondary',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'last_seen_at',
        'is_online',
        'ip_address',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'is_online'    => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────

    // Threshold online: 5 menit
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_seen_at) return false;
        return $this->last_seen_at->diffInMinutes(now()) < 5;
    }
}

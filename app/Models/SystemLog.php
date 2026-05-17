<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'level',
        'message',
        'context',
        'user_id',
        'url',
        'created_at',
    ];

    protected $casts = [
        'context'    => 'array',
        'created_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeErrors($query)
    {
        return $query->where('level', 'error');
    }

    public function scopeWarnings($query)
    {
        return $query->where('level', 'warning');
    }

    // ── Helpers ────────────────────────────────────────────

    // Static helper untuk log error dengan mudah
    public static function logError(string $message, array $context = [], ?int $userId = null): void
    {
        static::create([
            'level'      => 'error',
            'message'    => $message,
            'context'    => $context,
            'user_id'    => $userId,
            'url'        => request()->fullUrl(),
            'created_at' => now(),
        ]);
    }

    public static function logInfo(string $message, array $context = [], ?int $userId = null): void
    {
        static::create([
            'level'      => 'info',
            'message'    => $message,
            'context'    => $context,
            'user_id'    => $userId,
            'url'        => request()->fullUrl(),
            'created_at' => now(),
        ]);
    }
}

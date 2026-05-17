<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingCalendar extends Model
{
    protected $fillable = [
        'date',
        'type',
        'description',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeHolidays($query)
    {
        return $query->where('type', 'holiday');
    }

    public function scopeWorkingDays($query)
    {
        return $query->where('type', 'working');
    }

    // ── Helpers ────────────────────────────────────────────

    // Cek apakah tanggal tertentu adalah hari libur
    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        return static::where('date', $date->toDateString())
            ->whereIn('type', ['holiday', 'day_off'])
            ->exists();
    }

    // Cek apakah tanggal adalah hari kerja
    public static function isWorkingDay(\Carbon\Carbon $date): bool
    {
        // Sabtu & Minggu bukan hari kerja
        if ($date->isWeekend()) {
            // Kecuali ada entri 'working' (hari kerja pengganti)
            return static::where('date', $date->toDateString())
                ->where('type', 'working')
                ->exists();
        }

        // Senin–Jumat: hari kerja kecuali ada entri holiday/day_off
        return !static::isHoliday($date);
    }
}

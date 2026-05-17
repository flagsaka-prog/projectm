<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityService
{
    /**
     * Log aktivitas. Programmer tidak akan terekam.
     */
    public function log(string $description, array $properties = [], string $logName = 'system'): void
    {
        /** @var \App\Models\User $user */
        $user = auth::user();

        if (!$user) return;

        // Programmer invisible — tidak direkam
        if ($user->hasRole('Programmer')) return;

        activity($logName)
            ->causedBy($user)
            ->withProperties($properties)
            ->event('manual')
            ->log($description);
    }

    /**
     * Ambil log untuk CEO (tanpa Programmer, tapi Programmer sudah tidak direkam jadi aman)
     */
    public function getForCEO(int $perPage = 20)
    {
        return Activity::with('causer')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Ambil log untuk Programmer (untuk troubleshooting)
     */
    public function getForProgrammer(int $perPage = 20)
    {
        return Activity::with('causer')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Filter log
     */
    public function filter(array $filters = [], int $perPage = 20)
    {
        $query = Activity::with('causer');

        if (!empty($filters['causer_id'])) {
            $query->where('causer_id', $filters['causer_id']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }
}

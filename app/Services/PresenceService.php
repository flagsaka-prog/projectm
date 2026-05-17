<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\LoginHistory; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Cache;

class PresenceService
{

    // ── Update last seen user ──────────────────────────────
    public function updateLastSeen(User $user): void
    {
        // Simpan status online di Cache selama 5 menit
        Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));

        // Update database last_seen_at (dibatasi per menit agar tidak berat)
        $session = $user->session;
        if (!$session || $session->last_seen_at?->lt(now()->subMinute())) {
            UserSession::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'last_seen_at' => now(),
                    'ip_address'   => request()->ip(),
                ]
            );
        }
    }

    // ── Set user offline ───────────────────────────────────
    public function setOffline(User $user): void
    {
        // Hapus cache langsung saat logout
        Cache::forget('user-is-online-' . $user->id);

        // Update database user_sessions
        UserSession::where('user_id', $user->id)
            ->update(['is_online' => false]);

        // --- TAMBAHAN: Catat waktu logout di Login History ---
        $latestLog = \App\Models\LoginHistory::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first();

        if ($latestLog) {
            $latestLog->update(['logged_out_at' => now()]);
        }
    }

    // ── Ambil user online yang visible untuk viewer ────────
    public function getOnlineUsers(User $viewer): \Illuminate\Support\Collection
    {
        $role = $viewer->roles->first();
        if (!$role) return collect();

        $query = User::whereHas('session', function ($q) {
            $q->where('is_online', true);
        })
            ->with(['session', 'roles'])
            ->where('id', '!=', $viewer->id);

        // Programmer: lihat semua
        if ($role->is_system) {
            return $query->get();
        }

        // Semua role bisnis: exclude Programmer
        $query->whereHas('roles', function ($q) {
            $q->where('is_system', false);
        });

        // CEO: lihat semua kecuali Programmer (sudah di-exclude)
        if ($role->name === 'CEO') {
            return $query->get();
        }

        // Manager: lihat Manager/Admin lain di proyeknya sendiri
        if ($role->name === 'Manager') {
            return $query->where(function ($q) use ($viewer) {
                $q->whereHas('roles', fn($q2) => $q2->where('name', 'Manager'))
                    ->orWhere(fn($q2) => $q2->where('name', 'Admin'))
                    ->whereHas('projects', fn($q2) => $q2->where('assigned_manager_id', $viewer->id));
            })->get();
        }

        // Admin/Team Lead/Developer: lihat sesama proyek
        if (in_array($role->name, ['Admin', 'Team Lead', 'Developer'])) {
            $projectIds = $viewer->projects()->pluck('id');
            return $query->where(function ($q) use ($projectIds) {
                $q->whereHas('roles', fn($q2) => $q2->whereIn('name', ['Manager', 'Admin']))
                    ->whereHas('projects', fn($q2) => $q2->whereIn('id', $projectIds));
            })->get();
        }

        return collect();
    }

    // ── Format response untuk frontend ────────────────────

    public function formatForFrontend(User $viewer): array
    {
        return $this->getOnlineUsers($viewer)->map(function ($user) {
            return [
                'id'          => $user->id,
                'name'        => $user->name,
                'role'        => $user->getRoleName(),
                'last_seen'   => $user->session?->last_seen_at?->diffForHumans(),
                'is_online'   => true,
            ];
        })->values()->toArray();
    }
}

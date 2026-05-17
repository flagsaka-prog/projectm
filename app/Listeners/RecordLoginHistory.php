<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RecordLoginHistory
{
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */

        $user = $event->user;

        // Programmer invisible — tidak dicatat
        if ($user->hasRole('Programmer')) return;

        // Cegah duplikat dalam 2 menit terakhir
        $recent = LoginHistory::where('user_id', $user->id)
            ->where('logged_in_at', '>=', Carbon::now()->subMinutes(2))
            ->exists();

        if ($recent) return;

        LoginHistory::create([
            'user_id'      => $user->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}

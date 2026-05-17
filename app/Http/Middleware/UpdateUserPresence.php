<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserPresence
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            UserSession::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'last_seen_at' => now(),
                    'is_online'    => true,
                    'ip_address'   => $request->ip(),
                ]
            );
        }

        return $next($request);
    }
}

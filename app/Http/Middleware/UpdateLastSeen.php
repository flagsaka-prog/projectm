<?php

namespace App\Http\Middleware;

use App\Services\PresenceService;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth::check()) {
            app(PresenceService::class)->updateLastSeen(auth::user());
        }

        return $next($request);
    }
}

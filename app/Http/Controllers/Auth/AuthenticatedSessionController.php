<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();
        $request->session()->regenerate();

        $user = auth::user();

        // Blokir user tidak aktif
        if (!$user->is_active) {
            auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        // Update session online status
        app(\App\Services\PresenceService::class)->updateLastSeen($user);

        return $this->redirectBasedOnRole($user);
    }

    private function redirectBasedOnRole(\App\Models\User $user): RedirectResponse
    {
        $role = $user->roles->first()->name ?? 'Developer';

        $map = [
            'CEO'       => 'ceo.dashboard',
            'COO'       => 'coo.dashboard',
            'CTO'       => 'cto.dashboard',
            'CFO'       => 'cfo.dashboard',
            'VP'        => 'vp.dashboard',
            'PM'        => 'pm.dashboard',
            'Team Lead' => 'team-lead.dashboard',
            'Developer' => 'developer.dashboard',
            'Programmer' => 'programmer.dashboard',
        ];

        return redirect()->route($map[$role] ?? 'developer.dashboard');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Tangkap data user SEBELUM logout
        if (auth::check()) {
            $user = auth::user();

            // Set offline SEBELUM logout
            app(\App\Services\PresenceService::class)->setOffline($user);

            // Logout
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login');
        }

        // Jika user tidak login (misal session habis), tetap redirect ke login
        return redirect('/login');
    }
}

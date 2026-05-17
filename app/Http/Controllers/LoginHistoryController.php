<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginHistory::with('user')
            ->whereHas('user', fn($q) => $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'Programmer')))

            ->whereHas('user', fn($q) => $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'CEO')))
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('logged_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('logged_in_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(10);

        $users = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Programmer'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('login-history.index', compact('logs', 'users'));
    }
}

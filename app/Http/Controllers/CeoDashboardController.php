<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeoDashboardController extends Controller
{
    public function index()
    {
        $totalProjects = Project::count();
        $activeProjects = Project::whereNotIn('status', ['completed', 'cancelled'])->count();
        $unreadNotifs = Notification::where('user_id', auth::id())->whereNull('read_at')->count();

        $latestProjects = Project::with('manager')->latest()->take(5)->get();

        // Data untuk Grafik Donat
        $statusCounts = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Data untuk Grafik Batang
        $budgetVsActual = Project::whereNotIn('status', ['cancelled', 'archived'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'name' => strlen($p->name) > 15 ? substr($p->name, 0, 15) . '...' : $p->name,
                    'budget' => (float) $p->budget,
                    'actual' => (float) ($p->actual_cost ?? 0),
                ];
            });

        // GANTI 'ceo.dashboard' menjadi 'dashboards.ceo'
        return view('dashboards.ceo', compact(
            'totalProjects',
            'activeProjects',
            'unreadNotifs',
            'latestProjects',
            'statusCounts',
            'budgetVsActual'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $service = new ActivityService();

        $filters = [
            'causer_id'     => $request->get('causer_id'),
            'subject_type'  => $request->get('subject_type'),
            'date_from'     => $request->get('date_from'),
            'date_to'       => $request->get('date_to'),
        ];

        $logs = $service->filter($filters);

        // Data untuk dropdown filter
        $users = \App\Models\User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Programmer'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('audit-log.index', compact('logs', 'users', 'filters'));
    }
}

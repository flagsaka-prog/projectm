@extends('layouts.app')
@section('title',
    'Dashboard CFO
    ')
@section('page-title',
    'Dashboard CFO
    ')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Project</p>
                <p class="text-2xl font-bold mt-1">{{ \App\Models\Project::count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Aktif</p>
                <p class="text-2xl font-bold mt-1 text-blue-600">
                    {{ \App\Models\Project::where('status', 'active')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Task</p>
                <p class="text-2xl font-bold mt-1">{{ \App\Models\Task::count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-2xl font-bold mt-1 text-green-600">
                    {{ \App\Models\Task::where('status', 'completed')->count() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Project Terbaru</h3>
                <ul class="space-y-3">
                    @foreach (\App\Models\Project::latest()->take(5)->get() as $p)
                        <li class="flex justify-between items-center text-sm border-b pb-2">
                            <a href="{{ route('projects.show', $p) }}"
                                class="text-indigo-600 hover:underline">{{ $p->name }}</a>
                            <span class="text-gray-500">{{ $p->manager?->name ?? '-' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Aktivitas Terakhir</h3>
                <ul class="space-y-3">
                    @foreach (\App\Models\ActivityLog::latest()->take(5)->get() as $log)
                        <li class="text-sm text-gray-600 border-b pb-2">
                            <span class="font-medium">{{ $log->user->name ?? 'System' }}</span> — {{ $log->activity }}
                            <div class="text-xs text-gray-400 mt-1">{{ $log->created_at->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

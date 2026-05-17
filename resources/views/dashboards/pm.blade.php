@extends('layouts.app')

@section('title', 'Dashboard PM')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Project Saya</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Project::where('assigned_manager_id', auth()->id())->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Task Aktif</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Task::whereHas('project', fn($q) => $q->where('assigned_manager_id', auth()->id()))->whereNotIn('status', ['completed', 'cancelled'])->count() }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Notifikasi Belum Dibaca</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count() }}</p>
        </div>

    </div>

    {{-- Tabel task terbaru --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Task Terbaru</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Nama Task</th>
                    <th class="pb-2">Project</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\App\Models\Task::whereHas('project', fn($q) => $q->where('assigned_manager_id', auth()->id()))->with('project')->latest()->take(5)->get() as $task)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="py-3 font-medium">{{ $task->name }}</td>
                        <td class="py-3 text-gray-600">{{ $task->project->name ?? '-' }}</td>
                        <td class="py-3">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $task->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                    ">{{ ucfirst(str_replace('_', ' ', $task->status ?? '-')) }}</span>
                        </td>
                        <td class="py-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task->progress ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $task->progress ?? 0 }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">Belum ada task.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

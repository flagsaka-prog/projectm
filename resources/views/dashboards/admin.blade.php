@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Task Saya</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\TaskAssignment::where('user_id', auth()->id())->count() }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Task Selesai</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\TaskAssignment::where('user_id', auth()->id())->whereHas('task', fn($q) => $q->where('status', 'completed'))->count() }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Notifikasi Belum Dibaca</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count() }}
            </p>
        </div>

    </div>

    {{-- Task yang di-assign ke Admin ini --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Task Saya</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Nama Task</th>
                    <th class="pb-2">Project</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Progress</th>
                    <th class="pb-2">Deadline</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\App\Models\TaskAssignment::where('user_id', auth()->id())->with(['task.project'])->latest()->take(5)->get() as $assignment)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="py-3 font-medium">{{ $assignment->task->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $assignment->task->project->name ?? '-' }}</td>
                        <td class="py-3">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                            {{ $assignment->task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $assignment->task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $assignment->task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $assignment->task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        ">
                                {{ ucfirst(str_replace('_', ' ', $assignment->task->status ?? '-')) }}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full"
                                    style="width: {{ $assignment->task->progress ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $assignment->task->progress ?? 0 }}%</span>
                        </td>
                        <td class="py-3 text-gray-600">
                            {{ $assignment->task->end_date ? \Carbon\Carbon::parse($assignment->task->end_date)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-400">Belum ada task.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

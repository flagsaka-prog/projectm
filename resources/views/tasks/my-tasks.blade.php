@extends('layouts.app')

@section('title', 'Task Saya')
@section('page-title', 'Task Saya')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3">Nama Task</th>
                    <th class="px-6 py-3">Project</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Progress</th>
                    <th class="px-6 py-3">Deadline</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $assignment->task->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $assignment->task->project->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                        {{ $assignment->task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $assignment->task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $assignment->task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $assignment->task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $assignment->task->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                    ">{{ ucfirst(str_replace('_', ' ', $assignment->task->status ?? '-')) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mx-auto">
                                <div class="bg-blue-500 h-2 rounded-full"
                                    style="width: {{ $assignment->task->progress ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $assignment->task->progress ?? 0 }}%</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $assignment->task->end_date ? \Carbon\Carbon::parse($assignment->task->end_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tasks.show', [$assignment->task->project, $assignment->task]) }}"
                                class="text-blue-600 hover:underline text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada task.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $assignments->links() }}
@endsection

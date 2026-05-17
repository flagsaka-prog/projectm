@extends('layouts.app')
@section('title', 'Dashboard Team Lead')
@section('page-title', 'Dashboard Team Lead')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Task Saya</p>
                <p class="text-2xl font-bold mt-1">{{ auth()->user()->assignedTasks->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">In Progress</p>
                <p class="text-2xl font-bold mt-1 text-blue-600">
                    {{ auth()->user()->assignedTasks->where('status', 'in_progress')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-2xl font-bold mt-1 text-green-600">
                    {{ auth()->user()->assignedTasks->where('status', 'completed')->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Task Yang Ditugaskan Kepada Saya</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Project</th>
                        <th class="pb-2">Task</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- TAMBAHKAN whereHas('project') UNTUK MENYARING PROJECT YANG SUDAH DIHAPUS --}}
                    @forelse(auth()->user()->assignedTasks()->whereHas('project', function($q){
                                        $q->whereNull('deleted_at');
                                    })->latest()->take(10)->get() as $task)
                        <tr class="border-b last:border-0">
                            {{-- TAMBAHKAN ?-> DAN ?? '-' UNTUK MENCEGAH ERROR NULL --}}
                            <td class="py-3 text-gray-600">{{ $task->project?->name ?? '-' }}</td>
                            <td class="py-3 font-medium">{{ $task->name }}</td>
                            <td class="py-3">
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="py-3">
                                {{-- HANYA TAMPILKAN TOMBOL LIHAT JIKA PROJECT-nya MASIH ADA --}}
                                @if ($task->project)
                                    <a href="{{ route('tasks.show', [$task->project, $task]) }}"
                                        class="text-indigo-600 hover:underline">Lihat</a>
                                @else
                                    <span class="text-gray-400 text-xs">Project dihapus</span>
                                @endif
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
    </div>
@endsection

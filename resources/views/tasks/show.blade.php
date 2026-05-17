@extends('layouts.app')

@section('title', 'Detail Task')
@section('page-title', 'Detail Task')

@section('content')
    <div class="max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
            {{-- Info Utama --}}
            <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6 ">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-bold text-gray-800">{{ $task->name }}</h2>
                    <span
                        class="px-3 py-1 rounded-full text-xs
                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                    {{ $task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $task->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                ">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-6">
                    <div>
                        <p class="text-gray-500">Level</p>
                        <p class="font-medium text-gray-800">{{ $task->level }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Progress</p>
                        <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task->progress ?? 0 }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $task->progress ?? 0 }}%</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Durasi</p>
                        <p class="font-medium text-gray-800">{{ $task->duration ?? '-' }} hari</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Mulai</p>
                        <p class="font-medium text-gray-800">
                            @if ($task->start_date)
                                {{-- PERBAIKAN 1: Diubah dari $project ke $task --}}
                                {{ $task->start_date->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Selesai</p>
                        <p class="font-medium text-gray-800">
                            @if ($task->end_date)
                                {{-- PERBAIKAN 2: Diubah dari $project ke $task --}}
                                {{ $task->end_date->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Deskripsi</p>
                        <p class="font-medium text-gray-800">{{ $task->description ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    @can('task.update', $task)
                        <a href="{{ route('tasks.edit', [$project, $task]) }}"
                            class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600">Edit</a>
                    @endcan
                    <a href="{{ route('tasks.index', $project) }}"
                        class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">←
                        Kembali</a>
                </div>
            </div>

            {{-- Subtask --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-700">Subtask</h3>

                    {{-- PERBAIKAN 3: DITAMBAHKAN TOMBOL INI --}}
                    @can('task.create', $task)
                        <a href="{{ route('tasks.create', [$project, $task]) }}?parent_id={{ $task->id }}"
                            class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700">
                            + Tambah
                        </a>
                    @endcan
                </div>

                <div class="space-y-0">
                    @forelse($task->subtasks as $subtask)
                        <a href="{{ route('tasks.show', [$project, $subtask]) }}"
                            class="flex items-center justify-between py-2 border-b last:border-0 hover:bg-gray-50 rounded px-1">
                            <div class="min-w-0 mr-3">
                                <span class="text-gray-400 mr-1">↳</span>
                                <span class="text-sm font-medium truncate">{{ $subtask->name }}</span>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span
                                    class="px-2 py-1 rounded-full text-xs
                            {{ $subtask->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $subtask->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $subtask->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                        ">{{ ucfirst(str_replace('_', ' ', $subtask->status)) }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ $subtask->progress }}%</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada subtask.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Assignment --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Assignment</h3>
                @forelse($task->assignments as $assignment)
                    <div class="py-2 border-b last:border-0">
                        <p class="text-sm font-medium">{{ $assignment->user->name }}</p>
                        <p class="text-xs text-gray-500">Alokasi: {{ $assignment->allocation_percent }}% — Planned:
                            {{ $assignment->planned_hours }} jam</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada assignment.</p>
                @endforelse
                @can('task.assign')
                    <a href="{{ route('tasks.assignments', [$project, $task]) }}"
                        class="mt-4 block text-center bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+
                        Kelola Assignment</a>
                @endcan
            </div>

            {{-- Dependency & Resource --}}
            @canany(['task.dependency', 'task.resource'])
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-10">Lainnya</h3>
                    @can('task.dependency')
                        <a href="{{ route('tasks.dependencies.index', [$project, $task]) }}"
                            class="block text-center bg-purple-600 text-white px-4 py-2  rounded-lg text-sm hover:bg-purple-700 mb-3">🔗
                            Kelola Dependency</a>
                    @endcan
                    @can('task.resource')
                        <a href="{{ route('tasks.resources.index', [$project, $task]) }}"
                            class="block text-center bg-teal-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-teal-700">🔧
                            Kelola Resource</a>
                    @endcan
                </div>
            @endcanany
            {{-- Komentar --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">💬 Komentar</h3>
                <p class="text-sm text-gray-500">{{ $task->comments->count() }} komentar</p>
                @can('comment.view')
                    <a href="{{ route('tasks.comments.index', [$project, $task]) }}"
                        class="block text-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Lihat
                        Komentar</a>
                @endcan
            </div>
            {{-- Lampiran --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">📎 Lampiran</h3>
                <p class="text-sm text-gray-500">{{ $task->attachments->count() }} file</p>
                @can('attachment.view')
                    <a href="{{ route('tasks.attachments.index', [$project, $task]) }}"
                        class="block text-center bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-cyan-700">Lihat
                        Lampiran</a>
                @endcan
            </div>
            {{-- Timesheet --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">⏱️ Timesheet</h3>
                <p class="text-sm text-gray-500">Total: {{ $task->timesheets->sum('hours_worked') }} jam</p>
                <a href="{{ route('tasks.timesheets.index', [$project, $task]) }}"
                    class="block text-center bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-emerald-700">Lihat
                    Timesheet</a>
            </div>
        </div>
    </div>
@endsection

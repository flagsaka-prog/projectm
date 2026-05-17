@extends('layouts.app')

@section('title', 'Task — ' . $project->name)
@section('page-title', 'Task Proyek: ' . $project->name)

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">

            @can('task.create')
                <a href="{{ route('tasks.create', $project) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center">
                    + Tambah Task
                </a>
            @endcan

            @if (auth()->user()->hasRole('Admin'))
                <a href="{{ route('admin.my-tasks') }}"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
                    ← Kembali
                </a>
            @else
                <a href="{{ route('projects.show', $project) }}"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
                    ← Proyek
                </a>
            @endif

        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm min-w-[900px]">

            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3">Nama Task</th>
                    <th class="px-6 py-3 text-center">Level</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Progress</th>
                    <th class="px-6 py-3">Mulai</th>
                    <th class="px-6 py-3">Selesai</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tasks as $task)
                    @include('tasks._row', ['task' => $task, 'indent' => 0, 'project' => $project])
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada task.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

@endsection

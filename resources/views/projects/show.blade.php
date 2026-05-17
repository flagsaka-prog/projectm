@extends('layouts.app')

@section('title', 'Detail Project')
@section('page-title', 'Detail Project')

@section('content')
    <div class="max-w-5xl">

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
                <h2 class="text-xl font-bold text-gray-800">{{ $project->name }}</h2>

                <span
                    class="px-3 py-1 rounded-full text-xs w-fit
                    {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $project->status === 'active' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $project->status === 'on_hold' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $project->status === 'archived' ? 'bg-gray-100 text-gray-700' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">

                <div>
                    <p class="text-gray-500">PM</p>
                    <p class="font-medium text-gray-800">{{ $project->manager?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Tanggal Mulai</p>
                    <p class="font-medium text-gray-800">
                        @if ($project->start_date)
                            {{ $project->start_date->format('d-m-Y') }}
                            <div class="text-xs text-gray-400">
                                {{ $project->start_date->format('H:i') }}
                            </div>
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Tanggal Selesai</p>
                    <p class="font-medium text-gray-800">
                        @if ($project->end_date)
                            {{ $project->end_date->format('d-m-Y') }}
                            <div class="text-xs text-gray-400">
                                {{ $project->end_date->format('H:i') }}
                            </div>
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Budget</p>
                    <p class="font-medium text-gray-800">
                        Rp {{ number_format($project->budget, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Estimasi Cost</p>
                    <p class="font-medium text-gray-800">
                        Rp {{ number_format($project->estimated_cost ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Actual Cost</p>
                    <p class="font-medium text-gray-800">
                        Rp {{ number_format($project->actual_cost ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <p class="text-gray-500">Deskripsi</p>
                    <p class="font-medium text-gray-800 break-words">
                        {{ $project->description ?? '-' }}
                    </p>
                </div>

            </div>

            {{-- EVM --}}
            <div class="mt-6 pt-4 border-t border-gray-100">

                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    📈 Earned Value Management (EVM)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-sm">

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs">PV</p>
                        <p class="font-bold text-gray-800">
                            Rp {{ number_format($evm['pv'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs">EV</p>
                        <p class="font-bold text-gray-800">
                            Rp {{ number_format($evm['ev'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs">AC</p>
                        <p class="font-bold text-gray-800">
                            Rp {{ number_format($evm['ac'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs">SPI</p>
                        <p class="font-bold {{ $evm['spi'] >= 0.9 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $evm['spi'] }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $evm['planned_progress'] }}% vs {{ $evm['actual_progress'] }}%
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-500 text-xs">CPI</p>
                        <p class="font-bold {{ $evm['cpi'] >= 0.9 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $evm['cpi'] }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $evm['status'] }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3 mt-6 pt-4 border-t border-gray-100">

                <a href="{{ route('projects.edit', $project) }}"
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600 text-center">
                    Edit
                </a>

                @can('project.update')
                    <form method="POST" action="{{ route('projects.set-baseline', $project) }}" class="inline"
                        onsubmit="return confirm('Simpan baseline project ini?')">
                        @csrf
                        <button
                            class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm hover:bg-indigo-200 w-full sm:w-auto">
                            📌 Baseline
                        </button>
                    </form>
                @endcan

                @can('project.update')
                    <form method="POST" action="{{ route('projects.level-resources', $project) }}" class="inline"
                        onsubmit="return confirm('Level resources?')">
                        @csrf
                        <button
                            class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-sm hover:bg-orange-200 w-full sm:w-auto">
                            ⚖️ Level
                        </button>
                    </form>
                @endcan

                @can('project.archive')
                    @if ($project->status !== 'archived')
                        <form method="POST" action="{{ route('projects.archive', $project) }}" class="inline"
                            onsubmit="return confirm('Arsipkan project ini?')">
                            @csrf
                            <button
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 w-full sm:w-auto">
                                📦 Arsip
                            </button>
                        </form>
                    @endif
                @endcan

                <a href="{{ route('tasks.index', $project) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center">
                    Tasks
                </a>

                <a href="{{ route('projects.gantt.index', $project) }}"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 text-center">
                    Gantt
                </a>

                <a href="{{ route('reports.project.wbs-pdf', $project) }}"
                    class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900 text-center">
                    PDF
                </a>

                <a href="{{ $project->status === 'archived' ? route('projects.archived') : route('projects.index') }}"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
                    Kembali
                </a>

            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Members --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Anggota</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[300px]">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-2">Nama</th>
                                <th class="pb-2">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->members as $member)
                                <tr class="border-b last:border-0">
                                    <td class="py-3 font-medium">{{ $member->name }}</td>
                                    <td class="py-3 text-gray-600">{{ $member->pivot->role_in_project }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-gray-400">
                                        Belum ada anggota.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tasks --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Task</h3>

                @forelse($project->tasks as $task)
                    <div class="flex items-center justify-between py-2 border-b last:border-0 gap-3">

                        <span class="text-sm font-medium break-words">
                            {{ $task->name }}
                        </span>

                        <span
                            class="px-2 py-1 rounded-full text-xs whitespace-nowrap
                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>

                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada task.</p>
                @endforelse

            </div>

        </div>

    </div>
@endsection

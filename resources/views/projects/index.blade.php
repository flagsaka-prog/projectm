@extends('layouts.app')

@section('title', 'Daftar Project')
@section('page-title', 'Daftar Project')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <h3 class="text-lg font-semibold text-gray-700">Semua Project</h3>

        <a href="{{ route('projects.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center">
            + Tambah Project
        </a>

    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm min-w-[900px]">

            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                    <th class="px-6 py-3 whitespace-nowrap">PM</th>
                    <th class="px-6 py-3 whitespace-nowrap">Status</th>
                    <th class="px-6 py-3 whitespace-nowrap">Mulai</th>
                    <th class="px-6 py-3 whitespace-nowrap">Selesai</th>
                    <th class="px-6 py-3 whitespace-nowrap">Budget</th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium whitespace-nowrap">
                            {{ $project->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $project->Manager?->name ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">

                            <span
                                class="px-2 py-1 rounded-full text-xs
                                {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $project->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $project->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $project->status === 'archived' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($project->status) }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            @if ($project->start_date)
                                {{ $project->start_date->format('d-m-Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $project->start_date->format('H:i') }}
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            @if ($project->end_date)
                                {{ $project->end_date->format('d-m-Y') }}
                                <div class="text-xs text-gray-400">
                                    {{ $project->end_date->format('H:i') }}
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ number_format($project->budget, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="class=px-6 py-3 space-x-2">

                                <a href="{{ route('projects.show', $project) }}"
                                    class="text-blue-600 hover:underline text-xs">
                                    Detail
                                </a>

                                @can('update', $project)
                                    <a href="{{ route('projects.edit', $project) }}"
                                        class="text-yellow-600 hover:underline text-xs">
                                        Edit
                                    </a>
                                @endcan

                                @can('archive', $project)
                                    @if ($project->status !== 'archived')
                                        <form method="POST" action="{{ route('projects.archive', $project) }}" class="inline"
                                            onsubmit="return confirm('Arsipkan project ini?')">
                                            @csrf
                                            <button class="text-gray-600 hover:underline text-xs">
                                                Arsip
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                @can('delete', $project)
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline"
                                        onsubmit="return confirm('Hapus proyek ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                @endcan

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada proyek.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>

@endsection

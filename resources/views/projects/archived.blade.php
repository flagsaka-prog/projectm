@extends('layouts.app')

@section('title', 'Project Diarsipkan')
@section('page-title', 'Project Diarsipkan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <h3 class="text-lg font-semibold text-gray-700">Daftar Project Diarsipkan</h3>

        <a href="{{ route('projects.index') }}"
            class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
            ← Kembali ke Project Aktif
        </a>

    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm min-w-[800px]">

            <thead>
                <tr class="text-left text-gray-500 bg-gray-50 border-b">
                    <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                    <th class="px-6 py-3 whitespace-nowrap">PM</th>
                    <th class="px-6 py-3 whitespace-nowrap">Status</th>
                    <th class="px-6 py-3 whitespace-nowrap">Mulai</th>
                    <th class="px-6 py-3 whitespace-nowrap">Selesai</th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($projects as $project)
                    <tr class="border-b last:border-0 hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium text-gray-600 whitespace-nowrap">
                            {{ $project->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            {{ $project->Manager?->name ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                Archived
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            @if ($project->start_date)
                                {{ $project->start_date->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            @if ($project->end_date)
                                {{ $project->end_date->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">

                                <a href="{{ route('projects.show', $project) }}"
                                    class="text-blue-600 hover:underline text-xs">
                                    Detail
                                </a>

                                @can('update', $project)
                                    <form method="POST" action="{{ route('projects.restore', $project) }}" class="inline"
                                        onsubmit="return confirm('Pulihkan project ini?')">
                                        @csrf
                                        <button class="text-green-600 hover:underline text-xs">
                                            🔄 Pulihkan
                                        </button>
                                    </form>
                                @endcan

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada project diarsipkan.
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

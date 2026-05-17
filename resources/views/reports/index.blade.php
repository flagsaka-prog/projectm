@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reporting Dashboard')

@section('content')
    <div class="max-w-6xl">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Project</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $projects->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Budget</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                    {{ number_format($projects->sum('budget'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Actual Cost</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                    {{ number_format($projects->sum('actual_cost'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Over Budget</p>
                <p class="text-2xl font-bold text-red-600 mt-1">
                    {{ $projects->filter(fn($p) => $p->actual_cost > $p->budget)->count() }}</p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="flex flex-col sm:flex-row flex-wrap gap-3 mb-6">

            <a href="{{ route('reports.evm-summary') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 text-center">
                📊 EVM Summary
            </a>

            <a href="{{ route('reports.late-tasks') }}"
                class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 text-center">
                ⚠️ Keterlambatan
            </a>

            <a href="{{ route('reports.resource-usage-summary') }}"
                class="bg-teal-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-teal-700 text-center">
                👥 Resource Usage
            </a>

            <a href="{{ route('reports.cost-budget') }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 text-center">
                💰 Cost & Budget
            </a>

            <form action="{{ route('reports.portfolio-pdf') }}" method="GET"
                class="flex flex-col sm:flex-row sm:items-end gap-2 w-full sm:w-auto">

                <input type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto">

                <input type="date" name="end_date" value="{{ now()->toDateString() }}" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto">

                <button type="submit"
                    class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900 w-full sm:w-auto">
                    🖨️ Export PDF
                </button>
            </form>

        </div>

        {{-- Project List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-6 py-3 whitespace-nowrap">Project</th>
                        <th class="px-6 py-3 whitespace-nowrap">Manager</th>
                        <th class="px-6 py-3 whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 whitespace-nowrap">Budget</th>
                        <th class="px-6 py-3 whitespace-nowrap">Actual Cost</th>
                        <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $project)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium whitespace-nowrap">{{ $project->name }}</td>
                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ $project->manager?->name ?? '-' }}
                            </td>

                            <td class="px-6 py-3 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 rounded-full text-xs
                                    {{ $project->status === 'active' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $project->status === 'on_hold' ? 'bg-orange-100 text-orange-700' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>

                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                Rp {{ number_format($project->budget, 0, ',', '.') }}
                            </td>

                            <td
                                class="px-6 py-3 whitespace-nowrap
                                {{ $project->actual_cost > $project->budget ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                Rp {{ number_format($project->actual_cost, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                                    <a href="{{ route('reports.project', $project) }}"
                                        class="text-blue-600 hover:underline text-xs">Detail</a>
                                    <a href="{{ route('reports.resource', $project) }}"
                                        class="text-green-600 hover:underline text-xs">Resource</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                Belum ada project.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

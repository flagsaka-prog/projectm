@extends('layouts.app')

@section('title', 'Dashboard CEO')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Project</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalProjects }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Project Aktif</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeProjects }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Notifikasi Belum Dibaca</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $unreadNotifs }}</p>
        </div>
    </div>

    {{-- AREA GRAFIK BARU --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Grafik Donat Status Project -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Distribusi Status Project</h3>
            <div id="status-chart"></div>
        </div>

        <!-- Grafik Batang Budget vs Actual -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Budget vs Actual Cost (Top 5)</h3>
            <div id="budget-chart"></div>
        </div>

    </div>
    {{-- END AREA GRAFIK BARU ---

    {{-- Tabel project terbaru --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Project Terbaru</h3>
            <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua →</a>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Nama Project</th>
                    <th class="pb-2">Manager</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Mulai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestProjects as $project)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="py-3 font-medium">{{ $project->name }}</td>
                        <td class="py-3 text-gray-600">{{ $project->manager->name ?? '-' }}</td>
                        <td class="py-3">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $project->status === 'active' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $project->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $project->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-600">
                            {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">Belum ada project.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- SCRIPT UNTUK MEMBENTUK GRAFIK --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // 1. GRAFIK DONAT STATUS
                const statusOptions = {
                    series: [{
                        name: 'Project',
                        data: [
                            {{ $statusCounts['planning'] ?? 0 }},
                            {{ ($statusCounts['active'] ?? 0) + ($statusCounts['in_progress'] ?? 0) }},
                            {{ $statusCounts['on_hold'] ?? 0 }},
                            {{ $statusCounts['completed'] ?? 0 }},
                            {{ $statusCounts['cancelled'] ?? 0 }}
                        ]
                    }],
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    labels: ['Planning', 'Aktif', 'On Hold', 'Selesai', 'Batal'],
                    colors: ['#FCD34D', '#60A5FA', '#FB923C', '#34D399', '#F87171'],
                    legend: {
                        position: 'bottom'
                    }
                };
                new ApexCharts(document.querySelector("#status-chart"), statusOptions).render();

                // 2. GRAFIK BATANG BUDGET VS ACTUAL
                const budgetOptions = {
                    series: [{
                            name: 'Budget',
                            data: {{ \Illuminate\Support\Js::from($budgetVsActual->pluck('budget')) }}
                        },
                        {
                            name: 'Actual Cost',
                            data: {{ \Illuminate\Support\Js::from($budgetVsActual->pluck('actual')) }}
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: {{ \Illuminate\Support\Js::from($budgetVsActual->pluck('name')) }}
                    },
                    colors: ['#6366F1', '#F43F5E'],
                    legend: {
                        position: 'top'
                    }
                };
                new ApexCharts(document.querySelector("#budget-chart"), budgetOptions).render();

            });
        </script>
    @endpush
@endsection

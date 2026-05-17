@extends('layouts.app')

@section('title', 'Laporan Keterlambatan')
@section('page-title', 'Laporan Keterlambatan Task')

@section('content')
    <div class="max-w-7xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-700">⚠️ Task Terlambat</h3>
            <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Reports</a>
        </div>

        @if ($lateTasks->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                <p class="text-4xl mb-2">✅</p>
                <p>Tidak ada task yang terlambat saat ini.</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <p class="text-sm text-gray-500">
                        Menampilkan <strong class="text-red-600">{{ $lateTasks->count() }}</strong> task yang sudah melewati
                        batas waktu (end_date) dan belum selesai.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-gray-500 uppercase text-xs bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Task</th>
                                <th class="px-4 py-3">Project</th>
                                <th class="px-4 py-3">PM</th>
                                <th class="px-4 py-3">Assignee</th>
                                <th class="px-4 py-3">Deadline</th>
                                <th class="px-4 py-3 text-center">Terlambat</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Vs Baseline</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($lateTasks as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row['task_name'] }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('projects.show', $row['project_id']) }}"
                                            class="text-blue-600 hover:underline">{{ $row['project_name'] }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $row['pm'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $row['assignee'] }}</td>
                                    <td class="px-4 py-3 text-red-600 whitespace-nowrap">{{ $row['end_date'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            {{ $row['late_days'] }} hari
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs {{ $row['status'] === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['late_vs_baseline'])
                                            <span class="text-red-600 text-xs font-bold">⚠️ Ya</span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

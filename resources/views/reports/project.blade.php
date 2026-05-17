@extends('layouts.app')

@section('title', 'Report — ' . $project->name)
@section('page-title', 'Report: ' . $project->name)

@section('content')
    <div class="max-w-6xl">
        <a href="{{ route('reports.index') }}"
            class="inline-block mb-4 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">←
            Kembali</a>

        {{-- Status Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Task</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statusReport['tasks']['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $statusReport['tasks']['done'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">In Progress</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $statusReport['tasks']['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Overdue</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $statusReport['tasks']['overdue'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Progress</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statusReport['progress'] }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- Cost Summary --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">💰 Cost Summary</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Budget</span><span class="font-medium">Rp
                            {{ number_format($statusReport['cost']['budget'], 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Estimated Cost</span><span
                            class="font-medium">Rp
                            {{ number_format($statusReport['cost']['estimated_cost'], 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Actual Cost</span><span
                            class="font-medium">Rp
                            {{ number_format($statusReport['cost']['actual_cost'], 0, ',', '.') }}</span></div>
                    <div class="flex justify-between border-t pt-2"><span class="text-gray-500">Budget Used</span><span
                            class="font-medium">{{ $statusReport['cost']['budget_used'] }}%</span></div>
                    @if ($statusReport['cost']['is_over_budget'])
                        <div class="px-3 py-2 bg-red-50 text-red-700 rounded-lg text-xs font-medium">⚠️ Over Budget</div>
                    @endif
                </div>
            </div>

            {{-- Resource Allocation --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">👥 Resource Allocation</h3>
                <div class="space-y-2 text-sm">
                    @forelse($resourceReport['resources'] as $res)
                        <div class="flex justify-between items-center py-1">
                            <div>
                                <span class="font-medium">{{ $res['user']['name'] }}</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $res['role'] }}</span>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs
                        {{ $res['is_over'] ? 'bg-red-100 text-red-700' : ($res['is_under'] ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}
                    ">{{ $res['total_allocation'] }}%</span>
                        </div>
                    @empty
                        <p class="text-gray-400">Tidak ada data.</p>
                    @endforelse
                </div>
                @if ($resourceReport['over_count'] > 0)
                    <p class="text-xs text-red-500 mt-3">⚠️ {{ $resourceReport['over_count'] }} user over-allocated</p>
                @endif
            </div>
        </div>

        {{-- Task Progress Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <h3 class="font-semibold text-gray-700 px-6 py-4">📋 Task Progress</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-6 py-3">Task</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Progress</th>
                        <th class="px-6 py-3">Deadline</th>
                        <th class="px-6 py-3">Assignee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progressReport['tasks'] as $task)
                        <tr class="border-b last:border-0 {{ $task['is_overdue'] ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-6 py-2 font-medium whitespace-pre">{{ $task['name'] }}</td>
                            <td class="px-6 py-2">
                                @if ($task['is_overdue'])
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Overdue</span>
                                @else
                                    <span
                                        class="text-gray-500 text-xs">{{ ucfirst(str_replace('_', ' ', $task['status'])) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-2 text-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mx-auto">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task['progress'] }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $task['progress'] }}%</span>
                            </td>
                            <td class="px-6 py-2 text-gray-600 text-xs">
                                @if ($task['end_date'])
                                    {{ \Carbon\Carbon::parse($task['end_date'])->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-2 text-gray-600 text-xs">{{ $task['assignees'] ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada task.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

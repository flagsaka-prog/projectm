@extends('layouts.app')

@section('title', 'Resource Usage Summary')
@section('page-title', 'Laporan Penggunaan Resource')

@section('content')
    <div class="max-w-7xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-700">👥 Penggunaan Resource (Tim)</h3>
            <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Reports</a>
        </div>

        @if (empty($users))
            <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                <p class="text-4xl mb-2">👷</p>
                <p>Belum ada data assignment.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($users as $user)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        {{-- Header User --}}
                        <div
                            class="flex justify-between items-center px-6 py-4 border-b border-gray-100 {{ $user['is_over'] ? 'bg-red-50' : 'bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ substr($user['user_name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $user['user_name'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $user['role'] }} — {{ $user['task_count'] }} task
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Total Alokasi</p>
                                <p
                                    class="text-2xl font-bold {{ $user['is_over'] ? 'text-red-600' : ($user['total_allocation'] >= 80 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $user['total_allocation'] }}%
                                </p>
                                @if ($user['is_over'])
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 font-medium">⚠️
                                        Overallocated</span>
                                @endif
                            </div>
                        </div>

                        {{-- Detail Tasks --}}
                        <table class="w-full text-sm">
                            <thead class="text-left text-gray-500 uppercase text-xs bg-white border-b">
                                <tr>
                                    <th class="px-6 py-2">Project</th>
                                    <th class="px-6 py-2">Task</th>
                                    <th class="px-6 py-2 text-center">Alokasi</th>
                                    <th class="px-6 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($user['tasks'] as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-2">
                                            <a href="{{ route('projects.show', $task['project_id']) }}"
                                                class="text-blue-600 hover:underline">{{ $task['project_name'] }}</a>
                                        </td>
                                        <td class="px-6 py-2 text-gray-700">{{ $task['task_name'] }}</td>
                                        <td class="px-6 py-2 text-center">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs {{ $task['allocation'] > 50 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $task['allocation'] }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-2 text-center">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs {{ $task['status'] === 'in_progress' ? 'bg-blue-100 text-blue-700' : ($task['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task['status'])) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

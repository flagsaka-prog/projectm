@extends('layouts.app')

@section('title', 'Cost & Budget Report')
@section('page-title', 'Cost & Budget Report')

@section('content')
    <div class="max-w-5xl">
        <a href="{{ route('reports.index') }}"
            class="inline-block mb-4 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">←
            Kembali</a>

        {{-- Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Budget</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                    {{ number_format($report['totals']['budget'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Estimated</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">Rp
                    {{ number_format($report['totals']['estimated_cost'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Actual</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">Rp
                    {{ number_format($report['totals']['actual_cost'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Over Budget</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $report['totals']['over_budget'] }} project</p>
            </div>
        </div>

        {{-- Per Project --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Budget</th>
                        <th class="px-6 py-3">Estimated</th>
                        <th class="px-6 py-3">Actual</th>
                        <th class="px-6 py-3">Variance</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['projects'] as $p)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $p['project_name'] }}</td>
                            <td class="px-6 py-3 text-gray-600">Rp {{ number_format($p['budget'], 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-gray-600">Rp {{ number_format($p['estimated_cost'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 {{ $p['is_over_budget'] ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                Rp {{ number_format($p['actual_cost'], 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ $p['cost_variance'] < 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                                    Rp {{ number_format(abs($p['cost_variance']), 0, ',', '.') }}
                                    {{ $p['cost_variance'] < 0 ? '(defisit)' : '(surplus)' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if ($p['is_over_budget'])
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Over Budget</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

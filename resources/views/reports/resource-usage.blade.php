@extends('layouts.app')

@section('title', 'Resource Usage — ' . $project->name)
@section('page-title', 'Resource Usage: ' . $project->name)

@section('content')
    <div class="max-w-5xl">
        <a href="{{ route('reports.index') }}"
            class="inline-block mb-4 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">←
            Kembali</a>

        {{-- Conflict Warning --}}
        @if ($conflicts)
            <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-red-700 font-medium text-sm">⚠️ {{ count($conflicts) }} resource conflict ditemukan</p>
                @foreach ($conflicts as $c)
                    <p class="text-red-600 text-xs mt-1 ml-2">
                        {{ $c['resource']['name'] }} ({{ ucfirst($c['resource']['type']) }}) di task
                        "{{ $c['task']['name'] }}" bertabrakan dengan:
                        @foreach ($c['conflicts_with'] as $cw)
                            "{{ $cw['name'] }}" ({{ $cw['start_date'] }} s/d
                            {{ $cw['end_date'] }}){{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </p>
                @endforeach
            </div>
        @else
            <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-xl">
                <p class="text-green-700 font-medium text-sm">✅ Tidak ada resource conflict</p>
            </div>
        @endif

        {{-- Resource Usage Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-6 py-3">Resource</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3 text-center">Dipakai di</th>
                        <th class="px-6 py-3">Total Cost</th>
                        <th class="px-6 py-3">Detail Task</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usage as $u)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $u['resource']['name'] }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="px-2 py-1 rounded-full text-xs
                            {{ $u['resource']['type'] === 'human' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $u['resource']['type'] === 'equipment' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $u['resource']['type'] === 'material' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        ">{{ ucfirst($u['resource']['type']) }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $u['used_in_tasks'] > 1 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $u['used_in_tasks'] }} task
                                </span>
                            </td>
                            <td class="px-6 py-3 font-medium">Rp {{ number_format($u['total_cost'], 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                @foreach ($u['tasks'] as $t)
                                    <span class="block text-xs text-gray-600">
                                        {{ $t['name'] }}<br>
                                        <span class="text-gray-400">
                                            @if ($t['start_date'])
                                                {{ \Carbon\Carbon::parse($t['start_date'])->format('H:i:s') }}
                                                {{ \Carbon\Carbon::parse($t['start_date'])->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                            s/d
                                            @if ($t['end_date'])
                                                {{ \Carbon\Carbon::parse($t['end_date'])->format('H:i:s') }}
                                                {{ \Carbon\Carbon::parse($t['end_date'])->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada resource yang
                                digunakan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

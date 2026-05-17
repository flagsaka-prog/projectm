@extends('layouts.app')

@section('title', 'Error Logs')
@section('page-title', 'Error Logs')

@section('content')
    <div class="max-w-6xl">

        <div class="mb-4">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke Tools
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 px-6 py-4 border-b border-gray-100">

                <div>
                    <h3 class="font-semibold text-gray-700">Laravel Error Log</h3>
                    <p class="text-xs text-gray-400 mt-1">
                        Menampilkan 50 log terbaru dari storage/logs/laravel.log
                    </p>
                </div>

                <form action="{{ route('tools.log-clear') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin membersihkan semua log?')">
                    @csrf
                    <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200 w-full sm:w-auto">
                        🗑️ Bersihkan Log
                    </button>
                </form>

            </div>

            @if (empty($logs))
                <div class="px-6 py-12 text-center text-gray-400">
                    <p class="text-4xl mb-2">✅</p>
                    <p>Tidak ada error log. Sistem berjalan normal.</p>
                </div>
            @else
                <div class="overflow-x-auto">

                    <table class="w-full text-sm min-w-[900px]">

                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                <th class="px-6 py-3 text-left">Level</th>
                                <th class="px-6 py-3 text-left">Pesan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-3 text-gray-500 whitespace-nowrap text-xs">
                                        {{ \Carbon\Carbon::parse($log['datetime'])->format('d/m/Y H:i:s') }}
                                    </td>

                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @php
                                            $levelColor = match (strtolower($log['level'])) {
                                                'error' => 'bg-red-100 text-red-700',
                                                'warning' => 'bg-yellow-100 text-yellow-700',
                                                'critical' => 'bg-red-200 text-red-800',
                                                'info' => 'bg-blue-100 text-blue-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="px-2 py-1 rounded-full text-xs {{ $levelColor }}">
                                            {{ strtoupper($log['level']) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-gray-700 max-w-[400px]">
                                        <details>
                                            <summary class="cursor-pointer text-xs text-gray-600 hover:text-gray-900">
                                                {{ \Illuminate\Support\Str::limit($log['message'], 100) }}
                                            </summary>

                                            <pre class="mt-2 text-xs bg-gray-50 rounded p-3 overflow-x-auto whitespace-pre-wrap">
{{ $log['message'] }}
                                            </pre>
                                        </details>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>
@endsection

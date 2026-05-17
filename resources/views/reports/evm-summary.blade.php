@extends('layouts.app')

@section('title', 'EVM Summary Report')
@section('page-title', 'EVM Summary Report')

@section('content')
    <div class="max-w-7xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-700">📊 Portfolio EVM (Earned Value Management)</h3>
            <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Reports</a>
        </div>

        @if ($evmData->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                <p class="text-4xl mb-2">📭</p>
                <p>Belum ada project aktif.</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Project</th>
                                <th class="px-4 py-3 text-left">PM</th>
                                <th class="px-4 py-3 text-right">Budget</th>
                                <th class="px-4 py-3 text-right bg-blue-50">PV</th>
                                <th class="px-4 py-3 text-right bg-blue-50">EV</th>
                                <th class="px-4 py-3 text-right bg-blue-50">AC</th>
                                <th class="px-4 py-3 text-right bg-yellow-50">SV</th>
                                <th class="px-4 py-3 text-right bg-yellow-50">CV</th>
                                <th class="px-4 py-3 text-center bg-green-50">SPI</th>
                                <th class="px-4 py-3 text-center bg-green-50">CPI</th>
                                <th class="px-4 py-3 text-center">Plan</th>
                                <th class="px-4 py-3 text-center">Actual</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($evmData as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $row['pm'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp
                                        {{ number_format($row['budget'], 0, ',', '.') }}</td>

                                    {{-- EVM Core --}}
                                    <td class="px-4 py-3 text-right text-gray-700 bg-blue-50/50">Rp
                                        {{ number_format($row['pv'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800 bg-blue-50/50">Rp
                                        {{ number_format($row['ev'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700 bg-blue-50/50">Rp
                                        {{ number_format($row['ac'], 0, ',', '.') }}</td>

                                    {{-- Variances --}}
                                    <td
                                        class="px-4 py-3 text-right font-medium {{ $row['sv'] < 0 ? 'text-red-600' : 'text-green-600' }} bg-yellow-50/50">
                                        {{ $row['sv'] < 0 ? '-' : '' }}{{ number_format(abs($row['sv']), 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right font-medium {{ $row['cv'] < 0 ? 'text-red-600' : 'text-green-600' }} bg-yellow-50/50">
                                        {{ $row['cv'] < 0 ? '-' : '' }}{{ number_format(abs($row['cv']), 0, ',', '.') }}
                                    </td>

                                    {{-- Indices --}}
                                    <td class="px-4 py-3 text-center bg-green-50/50">
                                        @if ($row['spi'] !== null)
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-bold {{ $row['spi'] >= 0.9 ? 'bg-green-100 text-green-700' : ($row['spi'] >= 0.8 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $row['spi'] }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center bg-green-50/50">
                                        @if ($row['cpi'] !== null)
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-bold {{ $row['cpi'] >= 0.9 ? 'bg-green-100 text-green-700' : ($row['cpi'] >= 0.8 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $row['cpi'] }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Progress --}}
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $row['planned_progress'] }}%</td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-800">
                                        {{ $row['actual_progress'] }}%</td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs {{ $row['status'] === 'Sehat' ? 'bg-green-100 text-green-700' : ($row['status'] === 'Bermasalah' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Legend --}}
            <div class="mt-4 bg-white rounded-xl shadow-sm p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">📖 Keterangan Indeks:</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600">
                    <div>
                        <p class="font-medium text-gray-700 mb-1">SPI & CPI (Indeks):</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><span class="bg-green-100 text-green-700 px-1 rounded">&gt; 0.9</span> = Sehat</li>
                            <li><span class="bg-yellow-100 text-yellow-700 px-1 rounded">0.8 - 0.9</span> = Perlu Perhatian
                            </li>
                            <li><span class="bg-red-100 text-red-700 px-1 rounded">&lt; 0.8</span> = Bermasalah</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Variances (Selisih):</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li class="text-green-600">Positif = Di atas rencana (Good)</li>
                            <li class="text-red-600">Negatif (minus) = Di bawah rencana (Bad)</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700 mb-1">Kolom Utama:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>PV</strong> = Planned Value (Biaya seharusnya)</li>
                            <li><strong>EV</strong> = Earned Value (Biaya hasil kerja)</li>
                            <li><strong>AC</strong> = Actual Cost (Biaya riil keluar)</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

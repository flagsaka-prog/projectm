@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')
    <div class="max-w-6xl">
        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Tools</a>
            <div class="flex gap-3">
                <form action="{{ route('tools.download-audit-log') }}" method="GET">
                    <button class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm hover:bg-indigo-200">
                        📥 Download CSV
                    </button>
                </form>
                <form action="{{ route('tools.clear-audit-log') }}" method="POST"
                    onsubmit="return confirm('HAPUS SEMUA AUDIT LOG? Data tidak bisa dikembalikan!')">
                    @csrf
                    <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">
                        🗑️ Bersihkan Log
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Log Aktivitas User</h3>
                <p class="text-xs text-gray-400 mt-1">Menampilkan riwayat semua aksi yang dilakukan oleh pengguna sistem.
                </p>
            </div>

            @if ($logs->isEmpty())
                <div class="px-6 py-12 text-center text-gray-400">
                    <p class="text-4xl mb-2">📋</p>
                    <p>Belum ada log aktivitas.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Waktu</th>
                            <th class="px-6 py-3 text-left">User</th>
                            <th class="px-6 py-3 text-left">Aktivitas</th>
                            <th class="px-6 py-3 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500 whitespace-nowrap text-xs">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-3 text-gray-700 font-medium text-xs">
                                    {{ optional($log->causer)->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-3 text-gray-700 text-xs">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    <details>
                                        <summary class="cursor-pointer text-xs text-gray-400 hover:text-gray-600">
                                            Lihat Detail
                                        </summary>
                                        <pre class="mt-2 text-xs bg-gray-50 rounded p-3 overflow-x-auto whitespace-pre-wrap">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

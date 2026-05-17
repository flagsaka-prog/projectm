@extends('layouts.app')

@section('title', 'Login History')
@section('page-title', 'Login History')

@section('content')
    <div class="max-w-5xl">

        <div class="mb-4">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke Tools
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-4">

            <h3 class="font-semibold text-gray-700 mb-4">🔐 Login History</h3>
            <p class="text-xs text-gray-400 mb-4">
                Menampilkan riwayat masuk dan keluar user.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">

                <form action="{{ route('tools.login-history-download') }}" method="GET">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 w-full sm:w-auto">
                        📥 Download CSV
                    </button>
                </form>

                <form action="{{ route('tools.login-history-clear') }}" method="POST"
                    onsubmit="return confirm('HAPUS SEMUA DATA LOGIN HISTORY? Tidak bisa dikembalikan!')">
                    @csrf
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 w-full sm:w-auto">
                        🗑️ Clear History
                    </button>
                </form>

            </div>

        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

            <table class="w-full text-sm min-w-[800px]">

                <thead class="text-left text-gray-500 uppercase text-xs bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Waktu Login</th>
                        <th class="px-6 py-3">Waktu Logout</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">IP</th>
                        <th class="px-6 py-3">Browser</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                {{ $log->logged_in_at->format('d M Y H:i') }}
                            </td>

                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                @if ($log->logged_out_at)
                                    {{ $log->logged_out_at->format('d M Y H:i') }}
                                @else
                                    <span class="text-xs text-gray-400">Masih aktif</span>
                                @endif
                            </td>

                            <td class="px-6 py-3 font-medium whitespace-nowrap">
                                {{ $log->user->name ?? '-' }}
                            </td>

                            <td class="px-6 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                                {{ $log->ip_address }}
                            </td>

                            <td class="px-6 py-3 text-gray-500 text-xs max-w-[200px] truncate">
                                {{ $log->user_agent }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada data.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>

    </div>
@endsection

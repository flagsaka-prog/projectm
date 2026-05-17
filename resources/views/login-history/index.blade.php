@extends('layouts.app')

@section('title', 'Login History')
@section('page-title', 'Login History')

@section('content')
    <div class="max-w-7xl">

        {{-- Filter --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('login-history.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select name="user_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua User</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('login-history.index') }}"
                        class="flex-1 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Mobile Card --}}
        <div class="space-y-4 md:hidden">
            @forelse($logs as $log)
                @php
                    $ua = $log->user_agent ?? '';
                    $browser = 'Unknown';
                    $os = 'Unknown';

                    if (str_contains($ua, 'Edg/')) {
                        $browser = 'Edge';
                    } elseif (str_contains($ua, 'Chrome/')) {
                        $browser = 'Chrome';
                    } elseif (str_contains($ua, 'Firefox/')) {
                        $browser = 'Firefox';
                    } elseif (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome')) {
                        $browser = 'Safari';
                    } elseif (str_contains($ua, 'Opera') || str_contains($ua, 'OPR/')) {
                        $browser = 'Opera';
                    }

                    if (str_contains($ua, 'Windows NT 10')) {
                        $os = 'Windows 10/11';
                    } elseif (str_contains($ua, 'Windows NT 6.3')) {
                        $os = 'Windows 8.1';
                    } elseif (str_contains($ua, 'Mac OS X')) {
                        $os = 'macOS';
                    } elseif (str_contains($ua, 'Linux')) {
                        $os = 'Linux';
                    } elseif (str_contains($ua, 'Android')) {
                        $os = 'Android';
                    } elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
                        $os = 'iOS';
                    }
                @endphp

                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $log->user->name ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $browser }} on {{ $os }}
                            </p>
                        </div>

                        <span class="text-xs font-mono text-gray-500">
                            {{ $log->ip_address }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500">Login</span>
                            <span class="text-right text-gray-700">
                                {{ $log->logged_in_at->format('d M Y H:i') }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500">Logout</span>
                            <span class="text-right text-gray-700">
                                @if ($log->logged_out_at)
                                    {{ $log->logged_out_at->format('d M Y H:i') }}
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
                    Belum ada data.
                </div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 bg-gray-50 border-b">
                            <th class="px-6 py-3">Waktu Login</th>
                            <th class="px-6 py-3">Waktu Logout</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $log->logged_in_at->format('d M Y H:i') }}
                                </td>

                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                    @if ($log->logged_out_at)
                                        {{ $log->logged_out_at->format('d M Y H:i') }}
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 font-medium">
                                    {{ $log->user->name ?? '-' }}
                                </td>

                                <td class="px-6 py-3 text-gray-600 font-mono text-xs">
                                    {{ $log->ip_address }}
                                </td>

                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    {{ $browser }} on {{ $os }}
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
        </div>

        {{ $logs->links() }}
    </div>
@endsection

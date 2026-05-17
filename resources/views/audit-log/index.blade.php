@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')
    <div class="max-w-7xl">

        {{-- Filter --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('audit-log.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select name="causer_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua User</option>

                        @foreach ($users as $u)
                            <option value="{{ $u->id }}"
                                {{ ($filters['causer_id'] ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>

                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>

                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('audit-log.index') }}"
                        class="flex-1 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Mobile Card --}}
        <div class="space-y-4 md:hidden">
            @forelse($logs as $log)
                <div class="bg-white rounded-xl shadow-sm p-4">

                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $log->causer->name ?? '-' }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                            Audit
                        </span>
                    </div>

                    <div class="mb-3">
                        <p class="text-sm text-gray-700">
                            {{ $log->description }}
                        </p>
                    </div>

                    @if ($log->properties->count())
                        <button
                            onclick="document.getElementById('mobile-detail-{{ $log->id }}').classList.toggle('hidden')"
                            class="text-blue-600 hover:underline text-xs">
                            Lihat Detail
                        </button>

                        <div id="mobile-detail-{{ $log->id }}"
                            class="hidden mt-3 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 overflow-x-auto">
                            <pre>{{ json_encode($log->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
                    Belum ada log.
                </div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 bg-gray-50 border-b">
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Aktivitas</th>
                            <th class="px-6 py-3">Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="px-6 py-3 font-medium">
                                    {{ $log->causer->name ?? '-' }}
                                </td>

                                <td class="px-6 py-3">
                                    {{ $log->description }}
                                </td>

                                <td class="px-6 py-3">
                                    @if ($log->properties->count())
                                        <button
                                            onclick="document.getElementById('detail-{{ $log->id }}').classList.toggle('hidden')"
                                            class="text-blue-600 hover:underline text-xs">
                                            Lihat Detail
                                        </button>

                                        <div id="detail-{{ $log->id }}"
                                            class="hidden mt-2 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 max-h-48 overflow-y-auto overflow-x-auto">
                                            <pre>{{ json_encode($log->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada log.
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

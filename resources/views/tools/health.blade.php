@extends('layouts.app')

@section('title', 'System Health')
@section('page-title', 'System Health')

@section('content')
    <div class="max-w-5xl">
        <div class="mb-4">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Tools</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Server Info --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">🖥️ Server Info</h3>
                <div class="space-y-3 text-sm">
                    @foreach ([
            'PHP Version' => $data['php_version'],
            'Laravel Version' => $data['laravel_version'],
            'Environment' => $data['environment'],
            'Debug Mode' => $data['debug_mode'],
            'Timezone' => $data['timezone'],
            'Waktu Server' => $data['server_time'],
        ] as $label => $value)
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ $label }}</span>
                            <span
                                class="font-medium {{ $label === 'Debug Mode' && $data['debug_mode'] === 'Aktif ⚠️' ? 'text-red-600' : 'text-gray-800' }}">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Storage & DB --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">💾 Storage & Database</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Database ({{ $data['db_name'] }})</span>
                        <span class="font-medium text-gray-800">{{ $data['db_size'] }}</span>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500">Storage Digunakan</span>
                            <span class="font-medium text-gray-800">{{ $data['storage_used'] }} /
                                {{ $data['storage_total'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $data['storage_percent'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 text-right">{{ $data['storage_percent'] }}% terpakai</p>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Log Size</span>
                        <span class="font-medium text-gray-800">{{ $data['log_size'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Drivers --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">⚙️ Drivers</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-center">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 mb-1">Cache</p>
                    <p class="font-bold text-gray-800">{{ $data['cache_driver'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 mb-1">Session</p>
                    <p class="font-bold text-gray-800">{{ $data['session_driver'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 mb-1">DB Connection</p>
                    <p class="font-bold text-gray-800">{{ $data['db_connection'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 mb-1">Queue</p>
                    <p class="font-bold text-gray-800">{{ config('queue.default') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

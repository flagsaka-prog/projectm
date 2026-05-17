@extends('layouts.app')

@section('title', 'Tools')
@section('page-title', 'Programmer Tools')

@section('content')
    <div class="max-w-5xl">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- Server Info --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">ℹ️ Server Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">PHP Version</span>
                        <span class="font-medium">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Laravel Version</span>
                        <span class="font-medium">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Timezone</span>
                        <span class="font-medium">{{ config('app.timezone') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Environment</span>
                        <span class="font-medium">{{ config('app.env') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Maintenance Mode</span>
                        <span
                            class="font-medium {{ $isDown ? 'text-red-600' : 'text-green-600' }}">{{ $isDown ? 'ON' : 'OFF' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Log Size</span>
                        <span class="font-medium">{{ $logSize }}</span>
                    </div>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-3">
                    {{-- <form method="POST" action="{{ route('tools.cache-clear') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium">🗑️
                            Clear All Cache</button>
                    </form> --}}

                    @if ($isDown)
                        <form method="POST" action="{{ route('tools.maintenance') }}">
                            @csrf
                            <input type="hidden" name="action" value="disable">
                            <button type="submit"
                                class="w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg text-sm font-medium text-green-700">🟢
                                Disable Maintenance Mode</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('tools.migrate') }}"
                        onsubmit="return confirm('Jalankan migration? Pastikan tidak ada error di migration pending.')">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-sm font-medium text-blue-700">🔄
                            Run Pending Migration</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Menu Logs & Data --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-700 mb-4">📂 Logs & Data Management</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <a href="{{ route('tools.maintenance-view') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">🛠️</span>
                    <div>
                        <p class="text-gray-800">Maintenance</p>
                        <p class="text-xs text-gray-400 font-normal">Mode maintenance & secret key</p>
                    </div>
                </a>

                <a href="{{ route('tools.database-view') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">💾</span>
                    <div>
                        <p class="text-gray-800">Database</p>
                        <p class="text-xs text-gray-400 font-normal">Backup & restore</p>
                    </div>
                </a>

                <a href="{{ route('tools.error-logs') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">📄</span>
                    <div>
                        <p class="text-gray-800">Error Logs</p>
                        <p class="text-xs text-gray-400 font-normal">Log error sistem Laravel</p>
                    </div>
                </a>

                <a href="{{ route('tools.audit-logs') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">📋</span>
                    <div>
                        <p class="text-gray-800">Audit Logs</p>
                        <p class="text-xs text-gray-400 font-normal">Log aktivitas user & download</p>
                    </div>
                </a>

                <a href="{{ route('tools.health-view') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">❤️</span>
                    <div>
                        <p class="text-gray-800">Health</p>
                        <p class="text-xs text-gray-400 font-normal">Server & DB status</p>
                    </div>
                </a>

                <a href="{{ route('tools.cache-view') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">🧹</span>
                    <div>
                        <p class="text-gray-800">Cache</p>
                        <p class="text-xs text-gray-400 font-normal">Selective clear</p>
                    </div>
                </a>

                <a href="{{ route('tools.trashed-projects') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">🗑️</span>
                    <div>
                        <p class="text-gray-800">Trashed Projects</p>
                        <p class="text-xs text-gray-400 font-normal">Project yang dihapus sementara</p>
                    </div>
                </a>

                <a href="{{ route('tools.login-history-view') }}"
                    class="flex items-center gap-3 px-4 py-4 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium transition">
                    <span class="text-2xl">🔐</span>
                    <div>
                        <p class="text-gray-800">Login History</p>
                        <p class="text-xs text-gray-400 font-normal">Riwayat masuk & backup</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Migration Status --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">📦 Migration Status</h3>
            <div class="space-y-1 max-h-80 overflow-y-auto">
                @forelse($migrations as $m)
                    <div
                        class="flex items-center gap-3 px-3 py-2 text-sm rounded {{ $m['ran'] ? 'bg-green-50' : 'bg-yellow-50' }}">
                        <span>{{ $m['ran'] ? '✅' : '⏳' }}</span>
                        <span
                            class="{{ $m['ran'] ? 'text-gray-600' : 'text-yellow-700 font-medium' }}">{{ $m['file'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Tidak ada data migration.</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection

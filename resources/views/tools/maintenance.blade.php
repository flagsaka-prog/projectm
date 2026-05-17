@extends('layouts.app')

@section('title', 'Maintenance Mode')
@section('page-title', 'Maintenance Mode')

@section('content')
    <div class="max-w-2xl">
        <div class="mb-4">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Tools</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Status --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-full flex items-center justify-center text-2xl {{ $isDown ? 'bg-red-100' : 'bg-green-100' }}">
                    {{ $isDown ? '🔴' : '🟢' }}
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Status Sistem</p>
                    <p class="text-sm {{ $isDown ? 'text-red-600' : 'text-green-600' }} font-medium">
                        {{ $isDown ? 'Maintenance Mode AKTIF — Sistem sedang tidak dapat diakses publik' : 'Sistem Berjalan Normal' }}
                    </p>
                </div>
            </div>
        </div>

        @if (!$isDown)
            {{-- Aktifkan Maintenance --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-1">Aktifkan Maintenance Mode</h3>
                <p class="text-xs text-gray-400 mb-4">Sistem tidak bisa diakses pengguna selama maintenance. Hanya
                    Programmer yang bisa bypass menggunakan secret key.</p>

                <form action="{{ route('tools.maintenance') }}" method="POST" class="space-y-4"
                    onsubmit="return confirm('Yakin ingin mengaktifkan maintenance mode?')">
                    @csrf
                    <input type="hidden" name="action" value="enable">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="secret" placeholder="Minimal 6 karakter..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <p class="text-xs text-gray-400 mt-1">Gunakan key untuk bypass maintenance.<br> Contoh:<code
                                class="bg-gray-100 px-1 rounded">http://projectm.test/kode123</code>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan (opsional)</label>
                        <input type="text" name="message" placeholder="Contoh: Sedang dalam pemeliharaan sistem..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                        🔴 Aktifkan Maintenance
                    </button>
                </form>
            </div>
        @else
            {{-- Nonaktifkan Maintenance --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-1">Nonaktifkan Maintenance Mode</h3>
                <p class="text-xs text-gray-400 mb-4">Sistem akan kembali dapat diakses oleh semua pengguna.</p>

                <form action="{{ route('tools.maintenance') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menonaktifkan maintenance mode?')">
                    @csrf
                    <input type="hidden" name="action" value="disable">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                        🟢 Nonaktifkan Maintenance
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection

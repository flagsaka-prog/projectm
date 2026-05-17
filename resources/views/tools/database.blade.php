@extends('layouts.app')

@section('title', 'Database Management')
@section('page-title', 'Database Management')

@section('content')
    <div class="max-w-5xl">
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Backup & Download --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">💾 Backup Database</h3>

                <form action="{{ route('tools.database-download') }}" method="POST" class="mb-4">
                    @csrf
                    <p class="text-xs text-gray-400 mb-3">Download file SQL langsung ke komputer Anda.</p>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        ⬇️ Download Backup (.sql)
                    </button>
                </form>

                <form action="{{ route('tools.database-backup-server') }}" method="POST"
                    onsubmit="return confirm('Simpan backup ke server?')">
                    @csrf
                    <p class="text-xs text-gray-400 mb-3">Simpan file SQL di folder storage server.</p>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                        📂 Backup ke Server
                    </button>
                </form>
            </div>

            {{-- Restore --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">🔄 Restore Database</h3>

                <form action="{{ route('tools.database-restore') }}" method="POST" enctype="multipart/form-data"
                    class="mb-4">
                    @csrf
                    <p class="text-xs text-gray-400 mb-3">Upload file .sql dari komputer untuk me-restore database.</p>
                    <input type="file" name="sql_file" accept=".sql,.txt" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3">
                    <button type="submit"
                        class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg text-sm hover:bg-orange-700 font-medium"
                        onclick="return confirm('PERINGATAN: Data saat ini akan ditimpa oleh file backup. Lanjutkan?')">
                        ⬆️ Upload & Restore dari Komputer
                    </button>
                </form>

                @if (!empty($backups))
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-medium text-gray-500 mb-3">Atau restore dari file yang tersimpan di server:
                        </p>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach ($backups as $backup)
                                <form action="{{ route('tools.database-restore-server') }}" method="POST"
                                    class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg">
                                    @csrf
                                    <input type="hidden" name="filename" value="{{ $backup['name'] }}">
                                    <div class="text-xs">
                                        <span class="font-medium text-gray-700">{{ $backup['name'] }}</span>
                                        <span class="text-gray-400 ml-2">({{ $backup['size'] }} -
                                            {{ $backup['date'] }})</span>
                                    </div>
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium"
                                        onclick="return confirm('PERINGATAN: Restore database dengan file ini?')">
                                        Restore
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

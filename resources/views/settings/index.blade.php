@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')
@section('page-title', 'Pengaturan Aplikasi')

@section('content')
    <div class="max-w-2xl">

        {{-- FORM UPDATE --}}
        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Nama Aplikasi --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Identitas Aplikasi</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Nama Aplikasi
                    </label>

                    <input type="text" name="app_name" value="{{ old('app_name', $appName) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    <p class="text-xs text-gray-400 mt-1">
                        Akan mengubah nama di sidebar dan tab browser.
                    </p>
                </div>
            </div>

            {{-- Logo Aplikasi --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Logo Aplikasi
                </h3>

                @if ($logoPath)
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ Storage::url($logoPath) }}" alt="Logo"
                            class="w-[35px] h-[35px] object-contain rounded">

                        <button type="submit" form="delete-logo-form" class="text-xs text-red-500 hover:underline"
                            onclick="return confirm('Hapus logo ini?')">
                            Hapus Logo
                        </button>
                    </div>
                @else
                    <p class="text-sm text-gray-400 mb-4">
                        Belum ada logo diupload.
                    </p>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Upload Logo Baru
                    </label>

                    <input type="file" name="logo" accept="image/png,jpg,jpeg,svg"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                        file:mr-4 file:py-1 file:px-3
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">

                    <p class="text-xs text-gray-400 mt-1">
                        Format: PNG, JPG, SVG. Maks: 2MB.
                    </p>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        {{-- FORM DELETE LOGO --}}
        <form id="delete-logo-form" method="POST" action="{{ route('settings.delete-logo') }}" class="hidden">
            @csrf
        </form>

        {{-- Tombol Kembali --}}
        <div class="mt-6">
            <a href="javascript:history.back()"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                ← Kembali
            </a>
        </div>

    </div>
@endsection

@extends('layouts.app')

@section('title', 'Cache Management')
@section('page-title', 'Cache Management')

@section('content')
    <div class="max-w-3xl">
        <div class="mb-4">
            <a href="{{ route('tools.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Tools</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-2">🧹 Clear Cache Tertentu</h3>
            <p class="text-xs text-gray-400 mb-6">Pilih jenis cache yang ingin dibersihkan. Gunakan "Clear All" jika ingin
                membersihkan seluruh cache, view, config, route, dan event sekaligus.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                {{-- App Cache --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="app">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm transition border border-transparent hover:border-blue-200">
                        <p class="font-medium text-gray-800">📦 App Cache</p>
                        <p class="text-xs text-gray-400 mt-1">Cache aplikasi Laravel</p>
                    </button>
                </form>

                {{-- View Cache --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="view">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm transition border border-transparent hover:border-blue-200">
                        <p class="font-medium text-gray-800">👁️ View Cache</p>
                        <p class="text-xs text-gray-400 mt-1">Cache tampilan Blade</p>
                    </button>
                </form>

                {{-- Config Cache --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="config">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm transition border border-transparent hover:border-blue-200">
                        <p class="font-medium text-gray-800">⚙️ Config Cache</p>
                        <p class="text-xs text-gray-400 mt-1">Cache konfigurasi</p>
                    </button>
                </form>

                {{-- Route Cache --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="route">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm transition border border-transparent hover:border-blue-200">
                        <p class="font-medium text-gray-800">🛤️ Route Cache</p>
                        <p class="text-xs text-gray-400 mt-1">Cache route & middleware</p>
                    </button>
                </form>

                {{-- Event Cache --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="event">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm transition border border-transparent hover:border-blue-200">
                        <p class="font-medium text-gray-800">⚡ Event Cache</p>
                        <p class="text-xs text-gray-400 mt-1">Cache event listener</p>
                    </button>
                </form>

                {{-- Clear All --}}
                <form action="{{ route('tools.cache-clear-selected') }}" method="POST"
                    onsubmit="return confirm('Bersihkan SEMUA cache?')">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit"
                        class="w-full text-left px-4 py-4 bg-red-50 hover:bg-red-100 rounded-lg text-sm transition border border-transparent hover:border-red-200">
                        <p class="font-medium text-red-700">🗑️ Clear All</p>
                        <p class="text-xs text-red-400 mt-1">App, View, Config, Route, Event</p>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Akses Ditolak')
@section('content')
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center">
            <h1 class="text-7xl font-bold text-red-500 mb-4">403</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Akses Ditolak</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika ini adalah
                kesalahan.
            </p>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali ke Halaman Sebelumnya
            </a>
        </div>
    </div>
@endsection

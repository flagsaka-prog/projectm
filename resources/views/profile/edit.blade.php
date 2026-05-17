@extends('layouts.app')

@section('title', 'Profil Saya')
{{-- @section('page-title', 'Profil Saya') --}}

@section('content')
    <div class="max-w-5xl">
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="text-sm text-blue-600 hover:underline">← Kembali ke halaman
                sebelumnya</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 sm:p-8 bg-white rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Profil</h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ubah Password</h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard Programmer')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Resource</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Resource::count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">User Online</p>
            <p class="text-3xl font-bold text-gray-800 mt-1" id="online-count">—</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Notifikasi Belum Dibaca</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count() }}
            </p>
        </div>

    </div>

    <script>
        fetch("{{ route('presence.online') }}")
            .then(r => r.json())
            .then(data => document.getElementById('online-count').textContent = data.length);
    </script>
@endsection

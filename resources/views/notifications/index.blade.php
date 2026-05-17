@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
    <div class="max-w-3xl">

        {{-- Tombol Aksi Atas --}}
        @if ($notifications->count() > 0)
            <div class="mb-6 flex flex-wrap gap-3">
                @if ($notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">✓
                            Tandai Semua Sudah Dibaca</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('notifications.delete-all') }}" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">🗑️
                        Hapus Semua Notifikasi</button>
                </form>
            </div>
        @endif

        {{-- Daftar notifikasi --}}
        @forelse($notifications as $notif)
            <div
                class="mb-3 px-5 py-4 border border-gray-200 rounded-xl {{ $notif->read_at ? 'bg-white' : 'bg-blue-50 border-blue-200' }}">
                <div class="flex justify-between items-start">
                    <div>
                        @switch($notif->type)
                            @case('task_assigned')
                                <span>📋</span>
                            @break

                            @case('project_assigned')
                                <span>📁</span>
                            @break

                            @case('task_status_changed')
                                <span>🔄</span>
                            @break

                            @case('deadline_approaching')
                                <span>⚠️</span>
                            @break

                            @default
                                <span>🔔</span>
                        @endswitch

                        <span class="text-sm font-medium text-gray-800">{{ $notif->data['message'] ?? '-' }}</span>

                        @if (!$notif->read_at)
                            <span class="ml-2 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">Baru</span>
                        @endif
                    </div>
                    <span
                        class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $notif->created_at->diffForHumans() }}</span>
                </div>

                <div class="mt-3 flex items-center gap-4">
                    @if (!$notif->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notif) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:underline">Tandai Dibaca</button>
                        </form>
                    @else
                        <span class="text-xs text-gray-400">Dibaca {{ $notif->read_at->diffForHumans() }}</span>
                    @endif

                    {{-- Tombol Hapus Per Notifikasi --}}
                    <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" class="inline"
                        onsubmit="return confirm('Hapus notifikasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
                <div class="w-full text-center flex py-12 text-gray-400">Belum ada notifikasi.</div>
            @endforelse

            {{ $notifications->links() }}
        </div>
    @endsection

@extends('layouts.app')

@section('title', 'Trashed Projects')
@section('page-title', 'Trashed Projects')

@section('content')
    <div class="max-w-5xl">

        @forelse($projects as $project)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-500">Dihapus: {{ $project->deleted_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-400">Manager:
                            {{ $project->assigned_manager_id ? \App\Models\User::withTrashed()->find($project->assigned_manager_id)?->name ?? '-' : '-' }}
                        </p>
                    </div>
                    {{-- TAMBAHKAN DIV INI UNTUK MENAMPUNG 2 TOMBOL --}}
                    <div class="flex gap-2">
                        {{-- TOMBOL PULIHKAN BARU --}}
                        <form method="POST" action="{{ route('tools.restore-trashed-project', $project->id) }}"
                            onsubmit="return confirm('Pulihkan project ini?')">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">↩️
                                Pulihkan</button>
                        </form>

                        {{-- TOMBOL LAMA (HAPUS PERMANEN) --}}
                        <form method="POST" action="{{ route('tools.hard-delete-project', $project) }}"
                            onsubmit="return confirm('HAPUS PERMANEN? Data tidak bisa dikembalikan!')">
                            @csrf @method('DELETE')
                            <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">🗑️ Hapus
                                Permanen</button>
                        </form>
                    </div>
                    {{-- END TAMBAHKAN DIV --}}
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400 bg-white rounded-xl shadow-sm">
                <p>Tidak ada project di trash.</p>
            </div>
        @endforelse

        <div class="mt-6">
            <a href="{{ route('tools.index') }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← Kembali ke
                Tools</a>
        </div>
    </div>
@endsection

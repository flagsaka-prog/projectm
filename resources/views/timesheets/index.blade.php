@extends('layouts.app')

@section('title', 'Timesheet — ' . $task->name)
@section('page-title', 'Timesheet')

@section('content')
    <div class="max-w-4xl">
        <div class="mb-4 text-sm text-gray-500">Proyek: {{ $project->name }} — Task: {{ $task->name }}</div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Form Input --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Catat Jam Kerja</h3>
                <form method="POST" action="{{ route('tasks.timesheets.store', [$project, $task]) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                            <input type="date" name="work_date" value="{{ old('work_date', now()->toDateString()) }}"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Kerja *</label>
                            <input type="number" name="hours_worked" value="{{ old('hours_worked') }}" min="0.5"
                                max="24" step="0.5" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                </form>
            </div>

            {{-- Daftar Timesheet --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Riwayat</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-2">Tanggal</th>
                            <th class="pb-2">User</th>
                            <th class="pb-2">Jam</th>
                            <th class="pb-2">Keterangan</th>
                            <th class="pb-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timesheets as $ts)
                            <tr class="border-b last:border-0">
                                <td class="py-3">{{ $ts->work_date->format('d-m-Y') }}</td>
                                <td class="py-3">{{ $ts->user->name }}</td>
                                <td class="py-3 font-medium">{{ $ts->hours_worked }} jam</td>
                                <td class="py-3 text-gray-500 text-xs">{{ $ts->description ?? '-' }}</td>
                                <td class="py-3">
                                    <form method="POST"
                                        action="{{ route('tasks.timesheets.destroy', [$project, $task, $ts]) }}"
                                        class="inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('tasks.show', [$project, $task]) }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← Kembali ke
                Task</a>
        </div>
    </div>
@endsection

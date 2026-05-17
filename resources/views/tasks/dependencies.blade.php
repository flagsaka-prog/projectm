@extends('layouts.app')

@section('title', 'Kelola Dependency — ' . $task->name)
@section('page-title', 'Kelola Dependency')

@section('content')
    <div class="max-w-4xl">
        <div class="mb-4 text-sm text-gray-500">Project: {{ $project->name }} — Task: {{ $task->name }}</div>

        @error('dependency')
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Form Tambah --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Tambah Dependency</h3>
                <form method="POST" action="{{ route('tasks.dependencies.store', [$project, $task]) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Task yang harus selesai duluan
                                *</label>
                            <select name="depends_on_task_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Task --</option>
                                @foreach ($availableTasks as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Dependency</label>
                            <select name="type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="FS">FS — Finish to Start (paling umum)</option>
                                <option value="SS">SS — Start to Start</option>
                                <option value="FF">FF — Finish to Finish</option>
                                <option value="SF">SF — Start to Finish</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lag Days (jeda hari kerja)</label>
                            <input type="number" name="lag_days" value="0" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Tambah</button>
                </form>
            </div>

            {{-- Daftar Dependency --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Dependency Saat Ini</h3>
                @forelse($dependencies as $dep)
                    <div class="border border-gray-200 rounded-lg p-4 mb-3">
                        <div class="flex justify-between items-start">
                            <div class="text-sm">
                                <p class="font-medium text-gray-800">{{ $dep->dependsOnTask->name ?? '-' }}</p>
                                <p class="text-gray-500 mt-1">Tipe: <span class="font-medium">{{ $dep->type }}</span> —
                                    Lag: <span class="font-medium">{{ $dep->lag_days }} hari</span></p>
                            </div>
                            <form method="POST" action="{{ route('tasks.dependencies.destroy', [$project, $task, $dep]) }}"
                                onsubmit="return confirm('Hapus dependency ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada dependency.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('tasks.show', [$project, $task]) }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← Kembali ke
                Task</a>
        </div>
    </div>
@endsection

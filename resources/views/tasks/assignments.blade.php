@extends('layouts.app')

@section('title', 'Assignment — ' . $task->name)
@section('page-title', 'Assignment Task')

@section('content')
    <div class="max-w-4xl">
        <div class="mb-4 text-sm text-gray-500">Proyek: {{ $project->name }} — Task: {{ $task->name }}</div>


        @error('workload')
            <div class="mb-4 px-4 py-3 bg-orange-100 border border-orange-300 text-orange-800 rounded-lg">⚠️ {{ $message }}
            </div>
        @enderror

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Form Assign --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Assign Member</h3>
                <form method="POST" action="{{ route('tasks.assignments.store', [$project, $task]) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Member *</label>
                            <select name="user_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Member --</option>
                                @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}"
                                        {{ old('user_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alokasi (%) *</label>
                            <input type="number" name="allocation_percent" value="{{ old('allocation_percent', 100) }}"
                                min="1" max="100" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Planned Hours *</label>
                            <input type="number" name="planned_hours" value="{{ old('planned_hours') }}" min="0"
                                step="0.5" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Assign
                        Member</button>
                </form>
            </div>

            {{-- Daftar Assignment --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Daftar Assignment</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-2">Nama</th>
                            <th class="pb-2">Alokasi</th>
                            <th class="pb-2">Planned</th>
                            <th class="pb-2">Actual</th>
                            <th class="pb-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium">{{ $assignment->user->name }}</td>
                                <td class="py-3 text-gray-600">{{ $assignment->allocation_percent }}%</td>
                                <td class="py-3 text-gray-600">{{ $assignment->planned_hours }} jam</td>
                                <td class="py-3 text-gray-600">{{ $assignment->actual_hours ?? 0 }} jam</td>
                                <td class="py-3">
                                    <form method="POST"
                                        action="{{ route('tasks.assignments.destroy', [$project, $task, $assignment]) }}"
                                        class="inline" onsubmit="return confirm('Hapus assignment ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Belum ada assignment.</td>
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

@extends('layouts.app')

@section('title', 'Tambah Task')
@section('page-title', 'Tambah Task')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tasks.store', $project) }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Task *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Task (kosongkan jika task
                            utama)</label>
                        <select name="parent_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Task Utama (Level 1) --</option>
                            @foreach ($parentTasks as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ str_repeat('— ', $parent->level - 1) }}{{ $parent->name }} (Level
                                    {{ $parent->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_milestone" value="1" id="milestone_check"
                                class="rounded border-gray-300">
                            <span class="font-medium text-gray-700">🏁 Tandai sebagai Milestone (Durasi 0 hari)</span>
                        </label>
                    </div>

                    <div id="duration-section">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (hari kerja)</label>
                            <input type="number" name="duration" id="duration_input" value="{{ old('duration') }}"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Simpan Task</button>
                    <a href="{{ route('tasks.index', $project) }}"
                        class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('milestone_check').addEventListener('change', function() {
            const durSection = document.getElementById('duration-section');
            const durInput = document.getElementById('duration_input');
            if (this.checked) {
                durSection.style.opacity = '0.5';
                durSection.style.pointerEvents = 'none';
                durInput.value = 0;
            } else {
                durSection.style.opacity = '1';
                durSection.style.pointerEvents = 'auto';
                durInput.value = '';
            }
        });
    </script>
@endsection

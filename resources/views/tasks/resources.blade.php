@extends('layouts.app')

@section('title', 'Resource Task — ' . $task->name)
@section('page-title', 'Kelola Resource')

@section('content')
    <div class="max-w-4xl">
        <div class="mb-4 text-sm text-gray-500">Project: {{ $project->name }} — Task: {{ $task->name }}</div>

        @error('resource_id')
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Form Assign --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Assign Resource</h3>
                <form method="POST" action="{{ route('tasks.resources.store', [$project, $task]) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resource *</label>
                            <select name="resource_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Resource --</option>
                                @foreach ($availableResources as $resource)
                                    <option value="{{ $resource->id }}">{{ $resource->name }}
                                        ({{ ucfirst($resource->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alokasi (%)</label>
                            <input type="number" name="allocation_percent" value="100" min="1" max="100"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" name="quantity" value="1" min="0" step="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Cost</label>
                            <p class="text-sm text-gray-500">Otomatis dihitung saat resource di-assign</p>
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Assign</button>
                </form>
            </div>

            {{-- Daftar Resource Assigned --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Resource Assigned</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-2">Nama</th>
                            <th class="pb-2">Tipe</th>
                            <th class="pb-2">Alokasi</th>
                            <th class="pb-2">Qty</th>
                            <th class="pb-2">Est. Cost</th>
                            <th class="pb-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedResources as $resource)
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium">{{ $resource->name }}</td>
                                <td class="py-3 text-gray-600">{{ ucfirst($resource->type) }}</td>
                                <td class="py-3 text-gray-600">{{ $resource->pivot->allocation_percent }}%</td>
                                <td class="py-3 text-gray-600">{{ $resource->pivot->quantity }}</td>
                                <td class="py-3 text-gray-600">{{ number_format($resource->pivot->estimated_cost, 2) }}
                                </td>
                                <td class="py-3">
                                    <form method="POST"
                                        action="{{ route('tasks.resources.destroy', [$project, $task, $resource]) }}"
                                        class="inline" onsubmit="return confirm('Hapus resource ini dari task?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-400">Belum ada resource di-assign.</td>
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

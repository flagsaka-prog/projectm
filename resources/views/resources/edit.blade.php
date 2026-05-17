@extends('layouts.app')

@section('title', 'Edit Resource')
@section('page-title', 'Edit Resource')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">{{ $resource->name }}</h2>

            <form method="POST" action="{{ route('resources.update', $resource) }}">
                @csrf @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                        <input type="text" name="name" value="{{ old('name', $resource->name) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                        <select name="type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="human" {{ old('type', $resource->type) === 'human' ? 'selected' : '' }}>Human
                            </option>
                            <option value="equipment" {{ old('type', $resource->type) === 'equipment' ? 'selected' : '' }}>
                                Equipment</option>
                            <option value="material" {{ old('type', $resource->type) === 'material' ? 'selected' : '' }}>
                                Material</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Inisial</label>
                            <input type="text" name="initials" value="{{ old('initials', $resource->initials) }}"
                                maxlength="10"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                            <input type="text" name="group" value="{{ old('group', $resource->group) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Units</label>
                            <input type="number" name="max_units" value="{{ old('max_units', $resource->max_units) }}"
                                step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Std Rate (per jam)</label>
                            <input type="number" name="std_rate" value="{{ old('std_rate', $resource->std_rate) }}"
                                step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ovt Rate (per jam)</label>
                            <input type="number" name="ovt_rate" value="{{ old('ovt_rate', $resource->ovt_rate) }}"
                                step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost Per Use</label>
                            <input type="number" name="cost_per_use"
                                value="{{ old('cost_per_use', $resource->cost_per_use) }}" step="0.01" min="0"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Accrue At</label>
                        <select name="accrue_at"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih --</option>
                            <option value="start"
                                {{ old('accrue_at', $resource->accrue_at) === 'start' ? 'selected' : '' }}>Start</option>
                            <option value="end"
                                {{ old('accrue_at', $resource->accrue_at) === 'end' ? 'selected' : '' }}>End</option>
                            <option value="prorated"
                                {{ old('accrue_at', $resource->accrue_at) === 'prorated' ? 'selected' : '' }}>Prorated
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Update</button>
                    <a href="{{ route('resources.index') }}"
                        class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard VP')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Project</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Project::count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Project Aktif</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Project::whereNotIn('status', ['completed', 'cancelled', 'archived'])->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-500">Notifikasi Belum Dibaca</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">
                {{ \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count() }}</p>
        </div>

    </div>

    {{-- Tabel project terbaru --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Project Terbaru</h3>
            <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Nama Project</th>
                    <th class="pb-2">PM</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Mulai</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\App\Models\Project::with('Manager')->latest()->take(5)->get() as $project)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="py-3 font-medium">{{ $project->name }}</td>
                        <td class="py-3 text-gray-600">{{ $project->Manager->name ?? '-' }}</td>
                        <td class="py-3">
                            <span
                                class="px-2 py-1 rounded-full text-xs
                        {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $project->status === 'active' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $project->status === 'on_hold' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $project->status === 'archived' ? 'bg-gray-100 text-gray-700' : '' }}
                    ">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                        </td>
                        <td class="py-3 text-gray-600">
                            {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">Belum ada project.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

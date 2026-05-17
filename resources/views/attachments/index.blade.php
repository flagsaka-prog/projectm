@extends('layouts.app')

@section('title', 'Lampiran — ' . $task->name)
@section('page-title', 'Lampiran: ' . $task->name)

@section('content')
    <div class="max-w-4xl">

        {{-- @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    @error('file')
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">{{ $message }}</div>
    @enderror --}}

        {{-- Form Upload --}}
        @can('attachment.upload')
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="POST" action="{{ route('tasks.attachments.upload', [$project, $task]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center gap-3">
                        <input type="file" name="file" id="file-input"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 whitespace-nowrap">Upload</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Maksimal 10MB per file</p>
                </form>
            </div>
        @endcan

        {{-- Daftar File --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-6 py-3">File</th>
                        <th class="px-6 py-3">Diupload oleh</th>
                        <th class="px-6 py-3">Ukuran</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attachments as $attachment)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">
                                <span class="text-gray-400 mr-1">📄</span>
                                {{ $attachment->filename }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $attachment->user->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $attachment->readable_size }}</td>
                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">
                                {{ $attachment->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('tasks.attachments.download', [$project, $task, $attachment]) }}"
                                    class="text-blue-600 hover:underline text-xs">Download</a>
                                @can('attachment.delete')
                                    <form method="POST"
                                        action="{{ route('tasks.attachments.destroy', [$project, $task, $attachment]) }}"
                                        class="inline" onsubmit="return confirm('Hapus file ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada lampiran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('tasks.show', [$project, $task]) }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← Kembali ke
                Task</a>
        </div>
    </div>
@endsection

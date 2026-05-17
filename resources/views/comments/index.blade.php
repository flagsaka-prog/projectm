@extends('layouts.app')

@section('title', 'Komentar — ' . $task->name)
@section('page-title', 'Komentar: ' . $task->name)

@section('content')
    <div class="max-w-4xl">

        {{-- Form Komentar --}}
        @can('comment.create')
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="POST" action="{{ route('tasks.comments.store', [$project, $task]) }}">
                    @csrf
                    <textarea name="body" rows="3" placeholder="Tulis komentar..." required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Kirim</button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Daftar Komentar --}}
        @forelse($comments as $comment)
            <div class="bg-white rounded-xl shadow-sm p-5 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @can('comment.delete')
                        @if ($comment->user_id === auth()->id())
                            <form method="POST" action="{{ route('tasks.comments.destroy', [$project, $task, $comment]) }}"
                                onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Hapus</button>
                            </form>
                        @endif
                    @endcan
                </div>
                <p class="text-sm text-gray-700 mt-2 whitespace-pre-wrap">{{ $comment->body }}</p>

                {{-- Reply --}}
                <div class="mt-3 space-y-3">
                    @foreach ($comment->replies as $reply)
                        <div class="ml-6 pl-4 border-l-2 border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">{{ $reply->user->name }}</span>
                                    <span
                                        class="text-xs text-gray-400 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                @can('comment.delete')
                                    @if ($reply->user_id === auth()->id())
                                        <form method="POST"
                                            action="{{ route('tasks.comments.destroy', [$project, $task, $reply]) }}"
                                            onsubmit="return confirm('Hapus balasan ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 hover:underline text-xs">Hapus</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap">{{ $reply->body }}</p>
                        </div>
                    @endforeach

                    @can('comment.create')
                        <div class="ml-6 mt-2">
                            <form method="POST" action="{{ route('tasks.comments.store', [$project, $task]) }}"
                                class="flex gap-2">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <input type="text" name="body" placeholder="Balas komentar..." required
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="submit"
                                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Balas</button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">Belum ada komentar.</div>
        @endforelse

        <div class="mt-6">
            <a href="{{ route('tasks.show', [$project, $task]) }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">← Kembali ke
                Task</a>
        </div>
    </div>
@endsection

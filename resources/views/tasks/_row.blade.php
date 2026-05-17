<tr class="border-b last:border-0 hover:bg-gray-50">
    <td class="px-6 py-3 font-medium" style="padding-left: {{ 24 + $indent * 24 }}px">
        @if ($indent > 0)
            <span class="text-gray-400 mr-1">{{ str_repeat('↳ ', $indent) }}</span>
        @endif
        {{ $task->name }}
    </td>
    <td class="px-6 py-3 text-center text-gray-600">{{ $task->level }}</td>
    <td class="px-6 py-3">
        <span
            class="px-2 py-1 rounded-full text-xs
            {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
            {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
            {{ $task->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}
            {{ $task->status === 'on_hold' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $task->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
        ">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
    </td>
    <td class="px-6 py-3 text-center">
        <div class="w-16 bg-gray-200 rounded-full h-2 mx-auto">
            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task->progress ?? 0 }}%"></div>
        </div>
        <span class="text-xs text-gray-500">{{ $task->progress ?? 0 }}%</span>
    </td>

    {{-- PERBAIKAN 1: Diubah dari $project->start_date menjadi $task->start_date --}}
    <td class="px-6 py-3 text-gray-600">
        @if ($task->start_date)
            {{ $task->start_date->format('d-m-Y') }}
        @else
            -
        @endif
    </td>

    {{-- PERBAIKAN 2: Diubah dari $project->end_date menjadi $task->end_date --}}
    <td class="px-6 py-3 text-gray-600">
        @if ($task->end_date)
            {{ $task->end_date->format('d-m-Y') }}
        @else
            -
        @endif
    </td>

    <td class="px-6 py-3 space-x-2">
        <a href="{{ route('tasks.show', [$project, $task]) }}" class="text-blue-600 hover:underline text-xs">Detail</a>
        @can('task.update', $task)
            <a href="{{ route('tasks.edit', [$project, $task]) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
        @endcan
        @can('task.delete', $task)
            <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" class="inline"
                onsubmit="return confirm('Hapus task ini?')">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:underline text-xs">Hapus</button>
            </form>
        @endcan
    </td>
</tr>

@if ($task->subtasks && $task->subtasks->count())
    @foreach ($task->subtasks as $subtask)
        @include('tasks._row', ['task' => $subtask, 'indent' => $indent + 1, 'project' => $project])
    @endforeach
@endif

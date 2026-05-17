@extends('layouts.app')

@section('title', 'Gantt Chart — ' . $project->name)
@section('page-title', 'Gantt Chart: ' . $project->name)

@section('content')
    <div class="max-w-5xl">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">

        <div class="flex flex-wrap items-center gap-3 mb-6">
            <button onclick="ganttChart.change_view_mode('Day')"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Day</button>
            <button onclick="ganttChart.change_view_mode('Week')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Week</button>
            <button onclick="ganttChart.change_view_mode('Month')"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Month</button>
            <span class="text-xs text-gray-500 flex items-center gap-1"><span
                    class="inline-block w-3 h-3 bg-blue-500 rounded"></span>Task</span>
            <span class="text-xs text-gray-500 flex items-center gap-1"><span
                    class="inline-block w-3 h-3 bg-indigo-500 rounded"></span>Dengan Resource</span>
            <a href="{{ route('projects.show', $project) }}"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 ml-auto">← Kembali
                ke Project</a>
        </div>

        <div id="gantt-container" class="bg-white rounded-xl shadow-sm overflow-x-auto border border-gray-200 p-4">
            <div id="gantt"></div>
        </div>
    </div>

    <style>
        .bar-resource .bar-wrapper .bar {
            background: #6366f1 !important;
        }

        .bar-late .bar-wrapper .bar {
            background: #ef4444 !important;
        }

        .bar-critical .bar-wrapper .bar {
            background: #f97316 !important;
        }

        .bar-milestone .bar-wrapper .bar {
            background: #10b981 !important;
            border-radius: 0 !important;
            transform: rotate(45deg) scale(0.7) !important;
            margin-top: 10px !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
    <script>
        let ganttChart;

        fetch("{{ route('projects.gantt.data', $project) }}")
            .then(res => res.json())
            .then(tasks => {
                if (tasks.length === 0) {
                    document.getElementById('gantt-container').innerHTML =
                        '<p class="py-8 text-center text-gray-400">Belum ada task di project ini.</p>';
                    return;
                }

                ganttChart = new Gantt("#gantt", tasks, {
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    on_click: function(task) {
                        console.log('Task clicked:', task);
                    },
                    on_progress_change: function(task, progress) {
                        console.log('Progress changed:', task.name, progress);
                    },
                });
            })
            .catch(err => {
                document.getElementById('gantt-container').innerHTML =
                    '<p class="py-8 text-center text-red-500">Gagal memuat data Gantt.</p>';
                console.error(err);
            });
    </script>
@endsection

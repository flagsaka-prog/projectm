<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskBaseline;

class BaselineService
{
    public function setBaseline(Project $project): void
    {
        $tasks = Task::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->get();

        // Hapus baseline lama untuk project ini (sistem hanya menyimpan 1 baseline aktif)
        TaskBaseline::where('project_id', $project->id)->delete();

        foreach ($tasks as $task) {
            TaskBaseline::create([
                'project_id'     => $project->id,
                'task_id'        => $task->id,
                'task_name'      => $task->name,
                'start_date'     => $task->start_date,
                'end_date'       => $task->end_date,
                'duration'       => $task->duration ?? 0,
                'estimated_cost' => $task->resources->sum(function ($resource) {
                    return $resource->pivot->estimated_cost ?? 0;
                }),
            ]);
        }
    }
}

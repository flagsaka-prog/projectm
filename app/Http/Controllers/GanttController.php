<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GanttController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $this->authorize('view', $project);
        return view('gantt.index', compact('project'));
    }

    public function data(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = Task::where('project_id', $project->id)
            ->with(['dependencies.dependsOnTask', 'resources', 'latestBaseline'])
            ->get();

        // Hitung Critical Path
        $criticalIds = (new \App\Services\CriticalPathService())->getCriticalTaskIds($project->id);

        $ganttTasks = $tasks->map(function ($task) use ($criticalIds) {
            $resourceNames = $task->resources->pluck('name')->join(', ');

            $depInfo = $task->dependencies->map(function ($dep) {
                $name = $dep->dependsOnTask->name ?? '?';
                return "{$name} [{$dep->type}]";
            })->join(', ');

            $name = $task->name;
            if ($resourceNames) $name .= " ({$resourceNames})";
            if ($depInfo) $name .= " → {$depInfo}";

            $isLate = false;
            if ($task->latestBaseline && $task->end_date) {
                $baselineEnd = \Carbon\Carbon::parse($task->latestBaseline->end_date);
                $actualEnd = \Carbon\Carbon::parse($task->end_date);
                if ($actualEnd->gt($baselineEnd)) {
                    $isLate = true;
                    $name .= " ⚠️ Telat";
                }
            }

            $isCritical = in_array($task->id, $criticalIds);
            if ($isCritical) $name .= " 🔥";

            $isMilestone = $task->is_milestone;
            if ($isMilestone) $name = "🏁 " . $name;

            // Tentukan class berdasarkan prioritas
            $customClass = 'bar-default';
            if ($isMilestone) {
                $customClass = 'bar-milestone';
            } elseif ($isLate) {
                $customClass = 'bar-late';
            } elseif ($isCritical) {
                $customClass = 'bar-critical';
            } elseif ($resourceNames) {
                $customClass = 'bar-resource';
            }

            return [
                'id'           => (string) $task->id,
                'name'         => $name,
                'start'        => $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : now()->format('Y-m-d'),
                'end'          => ($isMilestone
                    ? \Carbon\Carbon::parse($task->start_date ?? now())->addDay()
                    : \Carbon\Carbon::parse($task->end_date ?? now()->addDays(1)))->format('Y-m-d'),
                'progress'     => $isMilestone ? 0 : ($task->progress ?? 0),
                'dependencies' => $task->dependencies->pluck('depends_on_task_id')->map(fn($id) => (string) $id)->join(', '),
                'custom_class' => $customClass,
            ];
        });

        return response()->json($ganttTasks);
    }
}

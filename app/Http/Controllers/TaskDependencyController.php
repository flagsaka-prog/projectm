<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Services\ActivityService;
use App\Services\DependencyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;


class TaskDependencyController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        // Kumpulkan ID yang harus dikecualikan (Diri sendiri + Anak langsung + Cucu)
        // Ini mencegah circular dependency (Task bergantung pada anaknya sendiri)
        $excludedIds = Task::where('project_id', $project->id)
            ->where('id', $task->id)
            ->orWhere('parent_id', $task->id)
            ->orWhereIn('parent_id', function ($q) use ($task) {
                $q->select('id')->from('tasks')->where('parent_id', $task->id);
            })
            ->pluck('id');

        // Ambil semua task lain di project ini (Termasuk task utama dan subtask lain)
        $availableTasks = Task::where('project_id', $project->id)
            ->whereNotIn('id', $excludedIds)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $dependencies = TaskDependency::where('task_id', $task->id)
            ->with('dependsOnTask')
            ->get();

        return view('tasks.dependencies', compact('project', 'task', 'availableTasks', 'dependencies'));
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'depends_on_task_id' => 'required|exists:tasks,id',
            'type'               => 'required|in:FS,SS,FF,SF',
            'lag_days'           => 'nullable|integer|min:0',
        ]);

        $service = new \App\Services\DependencyService();
        $result  = $service->addDependency(
            $task,
            $validated['depends_on_task_id'],
            $validated['type'],
            $validated['lag_days'] ?? 0
        );

        if (!$result['success']) {
            return back()->withErrors(['dependency' => $result['message']]);
        }

        (new \App\Services\ActivityService())->log("Menambahkan dependency: \"{$task->name}\" → task ID {$validated['depends_on_task_id']} ({$validated['type']})", ['task_id' => $task->id, 'project_id' => $project->id]);

        return back()->with('success', $result['message']);
    }

    public function destroy(Project $project, Task $task, TaskDependency $dependency)
    {
        $dependency->delete();

        (new \App\Services\ActivityService())->log("Menghapus dependency dari task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $project->id]);

        return back()->with('success', 'Dependency berhasil dihapus.');
    }
}

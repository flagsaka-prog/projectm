<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use App\Models\Task;
use App\Services\ActivityService;
use App\Services\CostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskResourceController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        $assignedResources = $task->resources;
        $availableResources = Resource::whereNotIn('id', $assignedResources->pluck('id'))
            ->latest()
            ->get();

        return view('tasks.resources', compact('project', 'task', 'assignedResources', 'availableResources'));
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'resource_id'        => 'required|exists:resources,id',
            'allocation_percent' => 'nullable|integer|min:1|max:100',
            'quantity'           => 'nullable|numeric|min:0',
        ]);

        if ($task->resources()->where('resource_id', $validated['resource_id'])->exists()) {
            return back()->withErrors(['resource_id' => 'Resource ini sudah di-assign ke task.']);
        }

        // Cek resource conflict HANYA untuk validasi, jangan masukkan attach di sini
        try {
            $workloadService = new \App\Services\WorkloadService();
            $workloadService->validateResourceConflict(
                $task,
                $validated['resource_id'],
                $validated['allocation_percent'] ?? 100,
                $validated['quantity'] ?? 1
            );
        } catch (\Exception $e) {
            return back()->withErrors(['resource_id' => $e->getMessage()]);
        }

        // PROSES ATTACH HARUS DI LUAR TRY-CATCH!
        $task->resources()->attach($validated['resource_id'], [
            'allocation_percent' => $validated['allocation_percent'] ?? 100,
            'quantity'           => $validated['quantity'] ?? 1,
            'estimated_cost'     => 0,
            'actual_cost'        => 0,
        ]);

        // Hitung ulang cost otomatis
        $task->load('resources');
        (new \App\Services\CostService())->updateTaskEstimatedCost($task);

        $resourceName = \App\Models\Resource::find($validated['resource_id'])->name;
        (new \App\Services\ActivityService())->log("Assign resource \"{$resourceName}\" ke task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $project->id, 'resource_id' => $validated['resource_id']]);

        return back()->with('success', 'Resource berhasil di-assign ke task.');
    }

    public function destroy(Project $project, Task $task, \App\Models\Resource $resource)
    {
        $this->authorize('view', $project);

        $task->resources()->detach($resource->id);

        // Hitung ulang cost setelah resource dihapus
        $task->load('resources');
        (new \App\Services\CostService())->updateTaskEstimatedCost($task);

        (new \App\Services\ActivityService())->log("Menghapus resource dari task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $project->id]);

        return back()->with('success', 'Resource berhasil dihapus dari task.');
    }
}

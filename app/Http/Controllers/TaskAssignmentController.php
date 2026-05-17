<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\CostService;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    use AuthorizesRequests;

    public function create(Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Team Lead', 'Developer']))
            ->where('is_active', true)
            ->get();

        $assignments = $task->assignments()->with('user')->get();

        return view('tasks.assignments', compact('project', 'task', 'admins', 'assignments'));
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'allocation_percent' => 'nullable|integer|min:1|max:100',
            'planned_hours'      => 'nullable|numeric|min:0',
        ]);

        $user              = \App\Models\User::find($validated['user_id']);
        $allocationPercent = $validated['allocation_percent'] ?? 100;

        // Cek workload conflict pakai WorkloadService yang sudah ada
        try {
            $workloadService = new \App\Services\WorkloadService();
            $workloadService->assign($task, $user, $allocationPercent, $validated['planned_hours'] ?? null);
        } catch (\Exception $e) {
            return back()->withErrors(['workload' => $e->getMessage()]);
        }

        // Recalculate cost
        $task->load('resources', 'assignments');
        (new \App\Services\CostService())->updateTaskEstimatedCost($task);

        // Kirim notifikasi
        $notifService = new \App\Services\NotificationService();
        $notifService->taskAssigned($task, $user);

        (new \App\Services\ActivityService())->log("Assign {$user->name} ke task \"{$task->name}\" (alokasi {$allocationPercent}%)", ['task_id' => $task->id, 'project_id' => $project->id, 'user_id' => $user->id]);

        return back()->with('success', 'User berhasil di-assign ke task.');
    }

    public function destroy(Project $project, Task $task, TaskAssignment $assignment)
    {
        $assignment->delete();

        // Recalculate cost
        $task->load('resources', 'assignments');
        (new \App\Services\CostService())->updateTaskEstimatedCost($task);

        (new \App\Services\ActivityService())->log("Menghapus {$assignment->user->name} dari task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $project->id, 'user_id' => $assignment->user_id]);

        return back()->with('success', 'User berhasil dihapus dari task.');
    }
}

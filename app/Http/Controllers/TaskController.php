<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\TaskProgressService;
use App\Services\StatusTransitionService;
use App\Models\TaskAssignment;
use App\Services\CostService;
use App\Services\ActivityService;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $tasks = Task::where('project_id', $project->id)
            ->whereNull('parent_id')
            ->with(['subtasks.subtasks'])
            ->orderBy('created_at')
            ->get();

        return view('tasks.index', compact('project', 'tasks'));
    }

    public function create(Project $project)
    {
        $parentTasks = Task::where('project_id', $project->id)
            ->where('level', '<', 3)
            ->whereNull('parent_id')
            ->orWhere(fn($q) => $q->where('project_id', $project->id)->where('level', 2))
            ->get();

        return view('tasks.create', compact('project', 'parentTasks'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:tasks,id',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'duration'    => 'nullable|integer|min:0',
        ]);

        // Tentukan level
        $level = 1;
        if (!empty($validated['parent_id'])) {
            $parent = Task::findOrFail($validated['parent_id']);
            $level  = $parent->level + 1;

            if ($level > 3) {
                return back()->withErrors(['parent_id' => 'Maksimal kedalaman task adalah 3 level.']);
            }
        }

        $validated['project_id']  = $project->id;
        $validated['level']       = $level;
        $validated['status']      = 'not_started';
        $validated['is_milestone'] = $request->has('is_milestone');
        if ($validated['is_milestone']) {
            $validated['duration'] = 0;
        }
        $validated['progress']    = 0;
        $validated['created_by']  = Auth::id();

        $task = Task::create($validated);

        (new ActivityService())->log("Membuat task \"{$validated['name']}\" di project {$project->name}", ['task_id' => $task->id, 'project_id' => $project->id]);

        return redirect()->route('tasks.index', $project)
            ->with('success', 'Task berhasil dibuat.');
    }

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['subtasks', 'assignments.user', 'dependencies', 'comments', 'attachments']);
        return view('tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $parentTasks = Task::where('project_id', $project->id)
            ->where('id', '!=', $task->id)
            ->where('level', '<', 3)
            ->get();

        return view('tasks.edit', compact('project', 'task', 'parentTasks'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'duration'    => 'nullable|integer|min:0',
            'status'      => 'required|in:not_started,in_progress,on_hold,completed,cancelled',
            'progress'    => 'required|integer|min:0|max:100',
        ]);

        // Validasi transisi status
        if ($validated['status'] !== $task->status) {
            $transitionService = new StatusTransitionService();

            if (!$transitionService->isAllowed($task, $validated['status'], auth::user())) {
                return back()
                    ->withErrors(['status' => $transitionService->getErrorMessage($task->status, $validated['status'])])
                    ->withInput();
            }
        }

        $task->update($validated);

        // ── OTOMATISASI STATUS PROJECT ──────────────────────
        // Jika task/subtask berubah status jadi 'in_progress' (Sedang Dikerjakan)
        if ($validated['status'] === 'in_progress' && $project->status === 'planning') {
            $project->update(['status' => 'active']);
            (new ActivityService())->log("Status project otomatis berubah menjadi 'Active' karena ada task yang dimulai", ['project_id' => $project->id]);
        }         // Opsional: Jika semua task di project sudah 'completed', project otomatis 'completed'
        elseif ($validated['status'] === 'completed') {
            $remainingTasks = $project->tasks()->whereNotIn('status', ['completed', 'cancelled'])->count();
            if ($remainingTasks === 0) {
                $project->update(['status' => 'completed']);
                (new ActivityService())->log("Status project otomatis berubah menjadi 'Completed' karena semua task telah selesai", ['project_id' => $project->id]);
            }
        }
        // ── END OTOMATISASI ─────────────────────────────────

        // Recalculate parent progress
        if ($task->parent_id) {
            $service = new TaskProgressService();
            $service->recalculate($task);
        }

        // Recalculate cost (duration bisa berubah)
        $task->load('resources');
        (new CostService())->updateTaskEstimatedCost($task);

        (new ActivityService())->log("Mengupdate task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $project->id, 'old_status' => $task->getOriginal('status'), 'new_status' => $validated['status']]);

        return redirect()->route('tasks.index', $project)
            ->with('success', 'Task berhasil diupdate.');
    }

    public function myTasks()
    {
        $assignments = TaskAssignment::where('user_id', auth::id())
            ->with(['task.project'])
            ->oldest() // UBAH DARI latest() MENJADI oldest()
            ->paginate(10);

        return view('tasks.my-tasks', compact('assignments'));
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $projectId = $task->project_id;
        $task->delete();

        (new ActivityService())->log("Menghapus task \"{$task->name}\"", ['task_id' => $task->id, 'project_id' => $projectId, 'deleted' => true]);

        // Recalculate project cost setelah task dihapus
        $project = Project::find($projectId);
        if ($project) {
            (new CostService())->recalculateProjectCost($project);
        }

        return redirect()->route('tasks.index', $project)
            ->with('success', 'Task berhasil dihapus.');
    }
}

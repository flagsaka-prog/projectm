<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        protected CostService $costService,
        protected WorkloadService $workloadService
    ) {}

    // ── 1. Project Status Report ───────────────────────────

    public function projectStatusReport(Project $project): array
    {
        $tasks = $project->tasks()->whereNull('deleted_at')->get();

        $totalTasks     = $tasks->count();
        $doneTasks      = $tasks->where('status', 'done')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();
        $overdueTasks   = $tasks->where('status', '!=', 'done')
            ->where('status', '!=', 'cancelled')
            ->filter(fn($t) => $t->end_date && $t->end_date->isPast())
            ->count();

        return [
            'project'         => $project->only(['id', 'name', 'status', 'start_date', 'end_date']),
            'manager'         => $project->manager?->only(['id', 'name']),
            'progress'        => $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0,
            'tasks'           => [
                'total'       => $totalTasks,
                'done'        => $doneTasks,
                'in_progress' => $inProgressTasks,
                'overdue'     => $overdueTasks,
                'remaining'   => $totalTasks - $doneTasks,
            ],
            'cost'            => $this->costService->getProjectCostSummary($project),
            'generated_at'    => now()->toDateTimeString(),
        ];
    }

    // ── 2. Resource Allocation Report ─────────────────────

    public function resourceAllocationReport(Project $project): array
    {
        $members = $project->members()->with(['roles', 'taskAssignments' => function ($q) use ($project) {
            $q->whereHas('task', fn($t) => $t->where('project_id', $project->id));
        }])->get();

        $resources = [];
        foreach ($members as $user) {
            $totalAllocation = $user->taskAssignments->sum('allocation_percent');
            $resources[] = [
                'user'             => $user->only(['id', 'name']),
                'role'             => $user->getRoleName(),
                'total_allocation' => $totalAllocation,
                'is_over'          => $totalAllocation > 100,
                'is_under'         => $totalAllocation < 50,
                'task_count'       => $user->taskAssignments->count(),
            ];
        }

        return [
            'project'      => $project->only(['id', 'name']),
            'resources'    => $resources,
            'over_count'   => collect($resources)->where('is_over', true)->count(),
            'under_count'  => collect($resources)->where('is_under', true)->count(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    // ── 3. Task Progress Report ────────────────────────────

    public function taskProgressReport(Project $project, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Task::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->with(['assignments.user']);

        if ($startDate) $query->where('start_date', '>=', $startDate);
        if ($endDate)   $query->where('end_date',   '<=', $endDate);

        $tasks = $query->orderBy('level')->orderBy('created_at')->get();

        return [
            'project'      => $project->only(['id', 'name']),
            'period'       => ['start' => $startDate, 'end' => $endDate],
            'tasks'        => $tasks->map(function ($task) {
                return [
                    'id'          => $task->id,
                    'level'       => $task->level,
                    'name'        => str_repeat('  ', $task->level - 1) . $task->name,
                    'status'      => $task->status,
                    'progress'    => $task->progress,
                    'start_date'  => $task->start_date?->toDateString(),
                    'end_date'    => $task->end_date?->toDateString(),
                    'is_overdue'  => $task->end_date && $task->end_date->isPast() && $task->status !== 'done',
                    'assignees'   => $task->assignments->map(fn($a) => $a->user->name)->join(', '),
                ];
            }),
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    // ── 4. Cost & Budget Report ────────────────────────────

    public function costBudgetReport(User $user): array
    {
        $roleName = $user->roles->first()->name ?? null;

        $projects = Project::whereNull('deleted_at');

        if ($roleName === 'PM') {
            $projects->where('assigned_manager_id', $user->id);
        } elseif (in_array($roleName, ['Team Lead', 'Developer'])) {
            $projectIds = $user->assignedTasks()->pluck('project_id')->unique();
            $projects->whereIn('id', $projectIds);
        }
        // CEO, COO, VP, dll bisa lihat semua (tanpa filter tambahan)

        $projects = $projects->get();

        return [
            'projects'     => $projects->map(fn($p) => $this->costService->getProjectCostSummary($p)),
            'totals'       => [
                'budget'         => $projects->sum('budget'),
                'estimated_cost' => $projects->sum('estimated_cost'),
                'actual_cost'    => $projects->sum('actual_cost'),
                'over_budget'    => $projects->filter(fn($p) => $p->actual_cost > $p->budget)->count(),
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    // ── 5. Overallocated Resource Report ──────────────────

    public function overallocatedResourceReport(Project $project): array
    {
        $overAllocated = $this->workloadService->getOverAllocatedUsers($project->id);

        return [
            'project'      => $project->only(['id', 'name']),
            'overallocated' => $overAllocated,
            'total'        => count($overAllocated),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth::user();

        $roleName = $user->roles->first()->name ?? null;

        $query = Project::whereNull('deleted_at')
            ->where('status', '!=', 'archived')
            ->with('manager');

        if ($roleName === 'PM') {
            $query->where('assigned_manager_id', $user->id);
        } elseif (in_array($roleName, ['Team Lead', 'Developer'])) {
            $projectIds = $user->assignedTasks()
                ->pluck('project_id')
                ->unique();

            $query->whereIn('id', $projectIds);
        }

        $projects = $query->latest()->get();

        return view('reports.index', compact('projects'));
    }

    public function project(Project $project)
    {
        $statusReport    = $this->reportService->projectStatusReport($project);
        $progressReport  = $this->reportService->taskProgressReport($project);
        $resourceReport  = $this->reportService->resourceAllocationReport($project);
        $overAllocReport = $this->reportService->overallocatedResourceReport($project);

        return view('reports.project', compact('project', 'statusReport', 'progressReport', 'resourceReport', 'overAllocReport'));
    }

    public function costBudget()
    {
        /** @var \App\Models\User $user */
        $report = $this->reportService->costBudgetReport(auth::user());

        return view('reports.cost-budget', compact('report'));
    }

    public function resourceUsage(Project $project)
    {
        $conflicts = (new \App\Services\WorkloadService())->getResourceConflicts($project->id);

        $resources = \App\Models\Resource::whereHas('tasks', fn($q) => $q->where('project_id', $project->id))
            ->with(['tasks' => fn($q) => $q->where('project_id', $project->id)->whereNull('deleted_at')])
            ->get();

        $usage = $resources->map(function ($resource) {
            $totalCost = $resource->tasks->sum(function ($task) use ($resource) {
                return $task->resources()->where('resource_id', $resource->id)->first()?->pivot->estimated_cost ?? 0;
            });

            return [
                'resource'      => $resource->only(['id', 'name', 'type', 'std_rate', 'cost_per_use']),
                'used_in_tasks' => $resource->tasks->count(),
                'total_cost'    => round($totalCost, 2),
                'tasks'         => $resource->tasks->map(fn($t) => $t->only(['id', 'name', 'start_date', 'end_date', 'status']))->toArray(),
            ];
        });

        return view('reports.resource-usage', compact('project', 'usage', 'conflicts'));
    }

    public function evmSummary()
    {
        $projects = Project::whereNull('deleted_at')
            ->where('status', '!=', 'archived')
            ->with('baselines')
            ->get();

        $evmData = $projects->map(function (Project $project) {
            $evm = (new \App\Services\EvmService())->calculate($project);
            return [
                'id'              => $project->id,
                'name'            => $project->name,
                'pm'              => $project->manager?->name ?? '-',
                'budget'          => $project->budget,
                'pv'              => $evm['pv'],
                'ev'              => $evm['ev'],
                'ac'              => $evm['ac'],
                'sv'              => $evm['sv'],
                'cv'              => $evm['cv'],
                'spi'             => $evm['spi'],
                'cpi'             => $evm['cpi'],
                'planned_progress' => $evm['planned_progress'],
                'actual_progress'  => $evm['actual_progress'],
                'status'          => $evm['status'],
            ];
        });

        return view('reports.evm-summary', compact('evmData'));
    }

    public function lateTasksReport()
    {
        // Task yang melewati end_date dan belum selesai/cancelled
        $lateTasks = \App\Models\Task::whereNull('deleted_at')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('end_date', '<', now())
            ->with(['project', 'assignments.user'])
            ->latest()
            ->get()
            ->map(function ($task) {
                $lateDays = now()->diffInDays(\Carbon\Carbon::parse($task->end_date));
                $isLateVsBaseline = false;

                if ($task->latestBaseline && $task->end_date) {
                    $baselineEnd = \Carbon\Carbon::parse($task->latestBaseline->end_date);
                    if (\Carbon\Carbon::parse($task->end_date)->gt($baselineEnd)) {
                        $isLateVsBaseline = true;
                    }
                }

                return [
                    'task_name'     => $task->name,
                    'project_name' => $task->project->name,
                    'project_id'   => $task->project_id,
                    'pm'           => $task->project->manager?->name ?? '-',
                    'assignee'     => $task->assignments->pluck('user.name')->join(', ') ?: '-',
                    'end_date'     => $task->end_date->format('d-m-Y'),
                    'late_days'    => $lateDays,
                    'status'       => $task->status,
                    'late_vs_baseline' => $isLateVsBaseline,
                ];
            });

        return view('reports.late-tasks', compact('lateTasks'));
    }

    public function resourceUsageSummary()
    {
        // Ambil semua user yang pernah di-assign ke task (human resource)
        $users = \App\Models\User::whereHas('taskAssignments')
            ->with(['roles', 'taskAssignments.task.project'])
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['Programmer', 'CEO']))
            ->get()
            ->map(function ($user) {
                $assignments = $user->taskAssignments->filter(fn($a) => $a->task && $a->task->project);

                $totalAllocation = $assignments->sum('allocation_percent');

                $taskDetails = $assignments->map(function ($a) {
                    return [
                        'task_name'    => $a->task->name,
                        'project_name' => $a->task->project->name,
                        'project_id'   => $a->task->project->id,
                        'allocation'   => $a->allocation_percent,
                        'status'       => $a->task->status ?? '-',
                    ];
                })->sortBy('project_name')->values()->all();

                return [
                    'user_name'        => $user->name,
                    'role'             => $user->getRoleName(),
                    'total_allocation' => $totalAllocation,
                    'is_over'          => $totalAllocation > 100,
                    'task_count'       => $assignments->count(),
                    'tasks'            => $taskDetails,
                ];
            })
            ->sortByDesc('total_allocation')
            ->values()
            ->all();

        return view('reports.resource-usage-summary', compact('users'));
    }

    public function exportPortfolioPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $projects = Project::whereNull('deleted_at')
            ->where('status', '!=', 'archived')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with('baselines')
            ->get();

        $evmData = $projects->map(function ($project) {
            $evm = (new \App\Services\EvmService())->calculate($project);
            return [
                'name' => $project->name,
                'budget' => $project->budget,
                'pv' => $evm['pv'],
                'ev' => $evm['ev'],
                'ac' => $evm['ac'],
                'spi' => $evm['spi'],
                'cpi' => $evm['cpi'],
                'status' => $evm['status'],
            ];
        });

        $lateTasks = \App\Models\Task::whereNull('deleted_at')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('end_date', '<', now())
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with(['project'])
            ->latest()
            ->get()
            ->map(function ($task) {
                return [
                    'task_name' => $task->name,
                    'project_name' => $task->project->name,
                    'end_date' => $task->end_date->format('d-m-Y'),
                    'late_days' => now()->diffInDays(\Carbon\Carbon::parse($task->end_date)),
                    'status' => $task->status,
                ];
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.portfolio-summary', compact('evmData', 'lateTasks', 'startDate', 'endDate'))
            ->setPaper('A4', 'landscape')
            ->setOption('isPhpEnabled', true);

        return $pdf->stream('Laporan_Portofolio_' . now()->format('d-m-Y') . '.pdf');
    }

    public function exportProjectWbsPdf(Project $project)
    {
        $tasks = \App\Models\Task::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->whereNull('parent_id') // Hanya ambil task utama (Level 1)
            ->with('subtasks.subtasks') // Load 2 level ke bawah
            ->orderBy('created_at')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.project-wbs', compact('project', 'tasks'))
            ->setPaper('A4', 'portrait')
            ->setOption('isPhpEnabled', true);

        return $pdf->stream('WBS_' . str_replace(' ', '_', $project->name) . '.pdf');
    }
}

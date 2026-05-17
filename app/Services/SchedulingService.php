<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class SchedulingService
{
    public function __construct(
        protected DependencyService $dependencyService,
        protected TaskService $taskService
    ) {}

    // ── Dipanggil setiap kali task berubah tanggal ─────────

    public function onTaskDateChanged(Task $task): void
    {
        DB::transaction(function () use ($task) {
            // 1. Auto shift semua task yang bergantung
            $this->dependencyService->autoShift($task);

            // 2. Recalculate parent jika task ini punya parent
            if ($task->parent_id) {
                $this->taskService->recalculateParent($task->parent);
            }
        });
    }

    // ── Dipanggil saat task baru di-assign dependency ──────

    public function onDependencyAdded(Task $task): void
    {
        DB::transaction(function () use ($task) {
            // Recalculate tanggal task yang baru dapat dependency
            $this->dependencyService->recalculateTaskDate($task);

            // Shift semua downstream task
            $this->dependencyService->autoShift($task);

            // Recalculate parent
            if ($task->parent_id) {
                $this->taskService->recalculateParent($task->parent);
            }
        });
    }

    // ── Dipanggil saat Gantt di-drag ───────────────────────

    public function onGanttDrag(Task $task, string $newStartDate, string $newEndDate): Task
    {
        return DB::transaction(function () use ($task, $newStartDate, $newEndDate) {
            // Hitung durasi baru
            $duration = $this->taskService->countWorkingDays(
                \Carbon\Carbon::parse($newStartDate),
                \Carbon\Carbon::parse($newEndDate)
            );

            // Update task
            $task->update([
                'start_date' => $newStartDate,
                'end_date'   => $newEndDate,
                'duration'   => $duration,
            ]);

            // Trigger auto shift downstream
            $this->dependencyService->autoShift($task);

            // Recalculate parent
            if ($task->parent_id) {
                $this->taskService->recalculateParent($task->parent);
            }

            return $task->fresh();
        });
    }

    // ── Recalculate seluruh proyek dari awal ───────────────

    public function recalculateProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            // Ambil semua root task (Level 1) urut start_date
            $rootTasks = Task::where('project_id', $project->id)
                ->whereNull('parent_id')
                ->whereNull('deleted_at')
                ->orderBy('start_date')
                ->get();

            foreach ($rootTasks as $task) {
                // Recalculate dependency tiap task
                $this->dependencyService->recalculateTaskDate($task);

                // Recalculate children ke parent
                if ($task->children()->count() > 0) {
                    $this->taskService->recalculateParent($task);
                }
            }
        });
    }
}

<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskAssignment;
use Carbon\Carbon;

class WorkloadService
{
    // ── Validasi sebelum assign user ke task ───────────────

    public function validateAllocation(User $user, Task $task, int $allocationPercent): void
    {
        // Ambil total alokasi user di periode yang sama
        $totalAllocated = $this->getTotalAllocation($user, $task->start_date, $task->end_date, $task->id);

        if (($totalAllocated + $allocationPercent) > 100) {
            $remaining = 100 - $totalAllocated;
            throw new \Exception(
                "User {$user->name} sudah teralokasi {$totalAllocated}% di periode ini. " .
                    "Sisa kapasitas: {$remaining}%."
            );
        }
    }

    // ── Assign user ke task ────────────────────────────────

    public function assign(Task $task, User $user, int $allocationPercent = 100, ?float $plannedHours = null): TaskAssignment
    {
        // Validasi alokasi
        if ($task->start_date && $task->end_date) {
            $this->validateAllocation($user, $task, $allocationPercent);
        }

        return TaskAssignment::updateOrCreate(
            ['task_id' => $task->id, 'user_id' => $user->id],
            [
                'allocation_percent' => $allocationPercent,
                'planned_hours'      => $plannedHours,
            ]
        );
    }

    // ── Unassign user dari task ────────────────────────────

    public function unassign(Task $task, User $user): void
    {
        TaskAssignment::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    // ── Total alokasi user di periode tertentu ─────────────

    public function getTotalAllocation(User $user, $startDate, $endDate, ?int $excludeTaskId = null): int
    {
        if (!$startDate || !$endDate) return 0;

        $query = TaskAssignment::where('user_id', $user->id)
            ->whereHas('task', function ($q) use ($startDate, $endDate) {
                $q->whereNull('deleted_at')
                    ->where(function ($q2) use ($startDate, $endDate) {
                        // Task yang periodenya overlap
                        $q2->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $startDate);
                    });
            });

        if ($excludeTaskId) {
            $query->where('task_id', '!=', $excludeTaskId);
        }

        return (int) $query->sum('allocation_percent');
    }

    // ── Cek apakah user over-allocated ────────────────────

    public function isOverAllocated(User $user, $startDate, $endDate): bool
    {
        return $this->getTotalAllocation($user, $startDate, $endDate) > 100;
    }

    // ── Workload summary per user ──────────────────────────

    public function getUserWorkload(User $user): array
    {
        $assignments = TaskAssignment::where('user_id', $user->id)
            ->whereHas('task', function ($q) {
                $q->whereNull('deleted_at')
                    ->whereNotIn('status', ['done', 'cancelled']);
            })
            ->with('task')
            ->get();

        return [
            'user'              => $user->only(['id', 'name', 'email']),
            'total_tasks'       => $assignments->count(),
            'total_allocation'  => $assignments->sum('allocation_percent'),
            'is_over_allocated' => $assignments->sum('allocation_percent') > 100,
            'assignments'       => $assignments->map(function ($a) {
                return [
                    'task_id'           => $a->task_id,
                    'task_name'         => $a->task->name,
                    'allocation'        => $a->allocation_percent,
                    'start_date'        => $a->task->start_date,
                    'end_date'          => $a->task->end_date,
                    'status'            => $a->task->status,
                ];
            }),
        ];
    }

    // ── Semua user over-allocated dalam satu proyek ────────

    public function getOverAllocatedUsers(int $projectId): array
    {
        $assignments = TaskAssignment::whereHas('task', function ($q) use ($projectId) {
            $q->where('project_id', $projectId)->whereNull('deleted_at');
        })->with(['user', 'task'])->get();

        // Group by user
        $grouped = $assignments->groupBy('user_id');

        $overAllocated = [];

        foreach ($grouped as $userId => $userAssignments) {
            $total = $userAssignments->sum('allocation_percent');
            if ($total > 100) {
                $overAllocated[] = [
                    'user'       => $userAssignments->first()->user->only(['id', 'name', 'email']),
                    'total'      => $total,
                    'excess'     => $total - 100,
                    'tasks'      => $userAssignments->count(),
                ];
            }
        }

        return $overAllocated;
    }

    // ── Validasi resource conflict sebelum assign ───────────

    // ── Validasi resource conflict sebelum assign ───────────

    public function validateResourceConflict(Task $task, int $resourceId, int $allocationPercent = 100, float $quantity = 1): void
    {
        if (!$task->start_date || !$task->end_date) return;

        // Ambil data master resource untuk cek max_units
        $resource = \App\Models\Resource::find($resourceId);
        $maxUnits = $resource->max_units ?? 1;

        // Cari task lain yang pakai resource ini di tanggal yang overlap
        $overlappingTasks = \App\Models\Task::whereHas('resources', function ($q) use ($resourceId) {
            $q->where('resource_id', $resourceId);
        })
            ->where('id', '!=', $task->id)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($task) {
                $q->where('start_date', '<=', $task->end_date)
                    ->where('end_date', '>=', $task->start_date);
            })
            ->with(['resources' => fn($q) => $q->where('resource_id', $resourceId)])
            ->get();

        // Jika tidak ada yang overlap, langsung boleh
        if ($overlappingTasks->isEmpty()) {
            return;
        }

        // HITUNG 1: Cek Kapasitas Unit (Quantity)
        $totalUsedUnits = $overlappingTasks->sum(function ($t) {
            return $t->resources->first()?->pivot->quantity ?? 1;
        });

        if (($totalUsedUnits + $quantity) > $maxUnits) {
            $taskNames = $overlappingTasks->pluck('name')->join(', ');
            throw new \Exception(
                "Kapasitas unit resource '{$resource->name}' sudah penuh (Max {$maxUnits}). Digunakan {$totalUsedUnits} unit di task: {$taskNames}."
            );
        }

        // HITUNG 2: Cek Kapasitas Persentase Alokasi (Maks 100%)
        $totalAllocatedPercent = $overlappingTasks->sum(function ($t) {
            return $t->resources->first()?->pivot->allocation_percent ?? 100;
        });

        if (($totalAllocatedPercent + $allocationPercent) > 100) {
            $taskNames = $overlappingTasks->pluck('name')->join(', ');
            $remaining = 100 - $totalAllocatedPercent;
            throw new \Exception(
                "Alokasi persentase resource '{$resource->name}' melebihi 100%. Sudah teralokasi {$totalAllocatedPercent}% di task: {$taskNames}. Sisa: {$remaining}%."
            );
        }
    }

    // ── Cek semua resource conflict dalam satu project ──────

    public function getResourceConflicts(int $projectId): array
    {
        $tasks = \App\Models\Task::where('project_id', $projectId)
            ->whereNull('deleted_at')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->with(['resources'])
            ->get();

        $conflicts = [];

        foreach ($tasks as $task) {
            foreach ($task->resources as $resource) {
                $overlapping = \App\Models\Task::whereHas('resources', fn($q) => $q->where('resource_id', $resource->id))
                    ->where('id', '!=', $task->id)
                    ->whereNull('deleted_at')
                    ->where(function ($q) use ($task) {
                        $q->where('start_date', '<=', $task->end_date)
                            ->where('end_date', '>=', $task->start_date);
                    })
                    ->get(['id', 'name']);

                if ($overlapping->isNotEmpty()) {
                    $conflicts[] = [
                        'resource'   => $resource->only(['id', 'name', 'type']),
                        'task'       => $task->only(['id', 'name', 'start_date', 'end_date']),
                        'conflicts_with' => $overlapping->map(fn($t) => $t->only(['id', 'name', 'start_date', 'end_date']))->toArray(),
                    ];
                }
            }
        }

        return $conflicts;
    }
}

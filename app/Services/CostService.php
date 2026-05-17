<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Resource;
use Carbon\Carbon;

class CostService
{
    // ── Hitung estimated cost satu task ───────────────────

    public function calculateTaskEstimatedCost(Task $task): float
    {
        $total = 0;

        foreach ($task->resources as $resource) {
            $pivot = $resource->pivot;

            switch ($resource->accrue_at) {
                case 'start':
                case 'end':
                    // Cost/Use: biaya sekali pakai
                    $total += $resource->cost_per_use;

                    // Std Rate × durasi task (dalam hari)
                    if ($task->duration) {
                        $total += $resource->std_rate * $task->duration
                            * ($pivot->allocation_percent / 100);
                    }
                    break;

                case 'prorated':
                default:
                    // Proporsional berdasarkan durasi & alokasi
                    if ($task->duration) {
                        $total += $resource->std_rate * $task->duration
                            * ($pivot->allocation_percent / 100);
                    }
                    // Tambah cost per use
                    $total += $resource->cost_per_use;

                    // Jika material: hitung berdasarkan quantity × std_rate
                    if ($resource->type === 'material' && $pivot->quantity) {
                        $total += $resource->std_rate * $pivot->quantity;
                    }
                    break;
            }
        }

        return round($total, 2);
    }

    // ── Update estimated cost satu task ───────────────────

    public function updateTaskEstimatedCost(Task $task): void
    {
        $estimated = $this->calculateTaskEstimatedCost($task);

        // Update di pivot task_resource
        foreach ($task->resources as $resource) {
            $task->resources()->updateExistingPivot($resource->id, [
                'estimated_cost' => $this->calculateResourceCostForTask($resource, $task),
            ]);
        }

        // Recalculate project cost
        $this->recalculateProjectCost($task->project);
    }

    // ── Hitung cost satu resource untuk satu task ─────────

    private function calculateResourceCostForTask(Resource $resource, Task $task): float
    {
        $pivot = $task->resources()->where('resource_id', $resource->id)->first()?->pivot;
        if (!$pivot) return 0;

        $cost = $resource->cost_per_use;

        if ($task->duration) {
            $cost += $resource->std_rate * $task->duration
                * ($pivot->allocation_percent / 100);
        }

        if ($resource->type === 'material' && $pivot->quantity) {
            $cost += $resource->std_rate * $pivot->quantity;
        }

        return round($cost, 2);
    }

    // ── Update actual cost task ────────────────────────────

    public function updateTaskActualCost(Task $task, float $actualCost): void
    {
        // Update actual cost di semua resource task (proporsional)
        $resources = $task->resources;
        if ($resources->isEmpty()) return;

        $perResource = round($actualCost / $resources->count(), 2);

        foreach ($resources as $resource) {
            $task->resources()->updateExistingPivot($resource->id, [
                'actual_cost' => $perResource,
            ]);
        }

        // Recalculate project
        $this->recalculateProjectCost($task->project);
    }

    // ── Recalculate seluruh cost proyek ───────────────────

    public function recalculateProjectCost(Project $project): void
    {
        $tasks = $project->tasks()->with('resources')->whereNull('deleted_at')->get();

        $estimatedCost = 0;
        $actualCost    = 0;

        foreach ($tasks as $task) {
            foreach ($task->resources as $resource) {
                $estimatedCost += $resource->pivot->estimated_cost ?? 0;
                $actualCost    += $resource->pivot->actual_cost    ?? 0;
            }
        }

        $project->update([
            'estimated_cost' => round($estimatedCost, 2),
            'actual_cost'    => round($actualCost, 2),
            'cost_variance'  => round($estimatedCost - $actualCost, 2),
        ]);
    }

    // ── Summary cost untuk dashboard ──────────────────────

    public function getProjectCostSummary(Project $project): array
    {
        return [
            'project_id'     => $project->id,
            'project_name'   => $project->name,
            'budget'         => $project->budget,
            'estimated_cost' => $project->estimated_cost,
            'actual_cost'    => $project->actual_cost,
            'cost_variance'  => $project->cost_variance,
            'budget_used'    => $project->budget > 0
                ? round(($project->actual_cost / $project->budget) * 100, 1)
                : 0,
            'is_over_budget' => $project->actual_cost > $project->budget,
        ];
    }

    public function recalculateTaskActualCostFromTimesheet(Task $task): void
    {
        $totalHours = $task->timesheets()->sum('hours_worked');

        // Cari resource bertipe 'human' yang di-assign ke task ini
        $humanResource = $task->resources()->where('type', 'human')->first();

        if ($humanResource) {
            $stdRate = $humanResource->std_rate ?? 0;
            $actualCost = $totalHours * $stdRate;

            $task->resources()->updateExistingPivot($humanResource->id, [
                'actual_cost' => round($actualCost, 2),
            ]);
        }

        // Update actual cost di project
        $this->recalculateProjectCost($task->project);
    }
}

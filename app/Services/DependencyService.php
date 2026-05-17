<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Support\Collection;

class DependencyService
{
    /**
     * Tambah dependency baru, dengan cek circular terlebih dahulu
     */
    public function addDependency(Task $task, int $dependsOnTaskId, string $type = 'FS', int $lagDays = 0): array
    {
        // Tidak boleh depend pada diri sendiri
        if ($task->id === $dependsOnTaskId) {
            return ['success' => false, 'message' => 'Task tidak bisa depend pada dirinya sendiri.'];
        }

        // Cek circular dependency
        if ($this->wouldCreateCircular($task->id, $dependsOnTaskId)) {
            return ['success' => false, 'message' => 'Dependency ini akan membuat circular dependency.'];
        }

        // Cek apakah sudah ada
        $exists = TaskDependency::where('task_id', $task->id)
            ->where('depends_on_task_id', $dependsOnTaskId)
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'Dependency ini sudah ada.'];
        }

        TaskDependency::create([
            'task_id'             => $task->id,
            'depends_on_task_id'  => $dependsOnTaskId,
            'type'                => $type,
            'lag_days'            => $lagDays,
        ]);

        return ['success' => true, 'message' => 'Dependency berhasil ditambahkan.'];
    }

    /**
     * Cek apakah menambah dependency akan membuat circular
     * Menggunakan DFS (Depth First Search)
     */
    public function wouldCreateCircular(int $taskId, int $dependsOnTaskId): bool
    {
        // Jika dependsOnTaskId sudah depend (langsung/tidak langsung) pada taskId → circular
        return $this->isReachable($dependsOnTaskId, $taskId, []);
    }

    private function isReachable(int $fromTaskId, int $targetTaskId, array $visited): bool
    {
        if ($fromTaskId === $targetTaskId) return true;
        if (in_array($fromTaskId, $visited)) return false;

        $visited[] = $fromTaskId;

        $dependencies = TaskDependency::where('task_id', $fromTaskId)
            ->pluck('depends_on_task_id');

        foreach ($dependencies as $depId) {
            if ($this->isReachable($depId, $targetTaskId, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hitung start_date task berdasarkan dependency-nya
     */
    public function resolveStartDate(Task $task): ?\Carbon\Carbon
    {
        $dependencies = TaskDependency::where('task_id', $task->id)
            ->with('dependsOnTask')
            ->get();

        if ($dependencies->isEmpty()) return null;

        $latestDate = null;

        foreach ($dependencies as $dep) {
            $depTask = $dep->dependsOnTask;
            if (!$depTask) continue;

            $date = match ($dep->type) {
                'FS' => $depTask->end_date
                    ? \Carbon\Carbon::parse($depTask->end_date)->addWeekdays($dep->lag_days + 1)
                    : null,
                'SS' => $depTask->start_date
                    ? \Carbon\Carbon::parse($depTask->start_date)->addWeekdays($dep->lag_days)
                    : null,
                'FF' => null, // FF tidak mempengaruhi start_date
                'SF' => null, // SF jarang dipakai
                default => null,
            };

            if ($date && (!$latestDate || $date->gt($latestDate))) {
                $latestDate = $date;
            }
        }

        return $latestDate;
    }
}

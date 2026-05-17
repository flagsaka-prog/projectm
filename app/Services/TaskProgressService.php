<?php

namespace App\Services;

use App\Models\Task;

class TaskProgressService
{
    /**
     * Recalculate progress & duration untuk semua parent ke atas
     */
    public function recalculate(Task $task): void
    {
        $this->updateTaskProgress($task);

        if ($task->parent_id) {
            $parent = Task::find($task->parent_id);
            if ($parent) {
                $this->recalculate($parent);
            }
        }
    }

    private function updateTaskProgress(Task $task): void
    {
        $subtasks = Task::where('parent_id', $task->id)->get();

        if ($subtasks->isEmpty()) {
            // Task leaf (tidak punya anak) — progress diinput manual, skip
            return;
        }

        // Progress = rata-rata progress subtask
        $avgProgress = $subtasks->avg('progress');
        $task->progress = (int) round($avgProgress);

        // Duration = total duration subtask
        $totalDuration = $subtasks->sum('duration');
        if ($totalDuration > 0) {
            $task->duration = $totalDuration;
        }

        // Status otomatis
        $task->status = $this->resolveStatus($task->progress, $subtasks);

        $task->save();
    }

    private function resolveStatus(int $progress, $subtasks): string
    {
        if ($progress === 0) return 'not_started';
        if ($progress === 100) return 'completed';

        // Jika ada subtask yang on_hold → parent on_hold
        if ($subtasks->contains('status', 'on_hold')) return 'on_hold';

        return 'in_progress';
    }
}

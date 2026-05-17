<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

class ResourceLevelingService
{
    public function level(Project $project): array
    {
        $tasks = Task::where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->where('is_milestone', false) // Skip milestone
            ->with('assignments.user')
            ->orderBy('start_date')
            ->get();

        // Kelompokkan task berdasarkan user yang di-assign
        $userTasks = [];
        foreach ($tasks as $task) {
            foreach ($task->assignments as $assignment) {
                $userId = $assignment->user_id;
                if (!isset($userTasks[$userId])) {
                    $userTasks[$userId] = collect();
                }
                $userTasks[$userId]->push($task);
            }
        }

        $shiftedTasks = [];

        // Proses leveling per user
        foreach ($userTasks as $userId => $uTasks) {
            $uTasks = $uTasks->sortBy('start_date');
            $lastEnd = null;

            foreach ($uTasks as $task) {
                $taskStart = $task->start_date ? Carbon::parse($task->start_date) : null;
                $taskEnd = $task->end_date ? Carbon::parse($task->end_date) : null;

                if (!$taskStart || !$taskEnd) {
                    $lastEnd = $taskEnd;
                    continue;
                }

                // Cek apakah task ini bentrok dengan task sebelumnya
                if ($lastEnd && $taskStart->lte($lastEnd)) {
                    // Hitung durasi asli task
                    $duration = $task->duration ?? $taskStart->diffInDays($taskEnd);

                    // Geser start date ke setelah lastEnd
                    $newStart = $lastEnd->copy()->addDay();
                    $newEnd = $newStart->copy()->addDays($duration);

                    // Update task
                    $task->update([
                        'start_date' => $newStart->format('Y-m-d'),
                        'end_date'   => $newEnd->format('Y-m-d'),
                    ]);

                    $shiftedTasks[] = [
                        'name' => $task->name,
                        'old_start' => $taskStart->format('d-m-Y'),
                        'new_start' => $newStart->format('d-m-Y'),
                    ];

                    $lastEnd = $newEnd;
                } else {
                    $lastEnd = $taskEnd;
                }
            }
        }

        return $shiftedTasks;
    }
}

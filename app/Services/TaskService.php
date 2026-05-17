<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    // ── Create ─────────────────────────────────────────────

    public function create(array $data, User $createdBy): Task
    {
        return DB::transaction(function () use ($data, $createdBy) {

            // Tentukan level berdasarkan parent
            $level = 1;
            if (!empty($data['parent_id'])) {
                $parent = Task::findOrFail($data['parent_id']);
                $level  = $parent->level + 1;

                // Validasi maks 3 level
                if ($level > 3) {
                    throw new \Exception('Maksimal kedalaman task adalah 3 level.');
                }
            }

            $task = Task::create([
                'project_id'  => $data['project_id'],
                'parent_id'   => $data['parent_id'] ?? null,
                'level'       => $level,
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date'  => $level === 1 ? null : ($data['start_date'] ?? null),
                'end_date'    => $level === 1 ? null : ($data['end_date'] ?? null),
                'duration'    => $level === 1 ? null : ($data['duration'] ?? null),
                'progress'    => 0,
                'status'      => 'not_started',
                'created_by'  => $createdBy->id,
            ]);

            // Jika ada parent, recalculate parent
            if ($task->parent_id) {
                $this->recalculateParent($task->parent);
            }

            return $task;
        });
    }

    // ── Update ─────────────────────────────────────────────

    public function update(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {

            // Level 1 tidak boleh update tanggal & durasi manual
            if ($task->level === 1) {
                unset($data['start_date'], $data['end_date'], $data['duration']);
            }

            $task->update([
                'name'        => $data['name']        ?? $task->name,
                'description' => $data['description'] ?? $task->description,
                'start_date'  => $data['start_date']  ?? $task->start_date,
                'end_date'    => $data['end_date']     ?? $task->end_date,
                'duration'    => $data['duration']     ?? $task->duration,
                'progress'    => $data['progress']     ?? $task->progress,
            ]);

            // Recalculate parent jika ada
            if ($task->parent_id) {
                $this->recalculateParent($task->parent);
            }

            return $task->fresh();
        });
    }

    // ── Update Status ──────────────────────────────────────

    public function updateStatus(Task $task, string $newStatus, User $by): Task
    {
        $this->validateTransition($task->status, $newStatus, $by);

        $task->update(['status' => $newStatus]);

        // Jika done, set progress 100
        if ($newStatus === 'done') {
            $task->update(['progress' => 100]);
        }

        // Recalculate parent
        if ($task->parent_id) {
            $this->recalculateParent($task->parent);
        }

        return $task->fresh();
    }

    // ── Delete ─────────────────────────────────────────────

    public function delete(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $parent = $task->parent_id ? $task->parent : null;
            $task->delete();

            if ($parent) {
                $this->recalculateParent($parent);
            }
        });
    }

    // ── Recalculate Parent (Level 1 & 2) ──────────────────

    public function recalculateParent(Task $parent): void
    {
        $children = $parent->children()->whereNull('deleted_at')->get();

        if ($children->isEmpty()) return;

        // Progress: rata-rata dari semua child
        $avgProgress = $children->avg('progress');

        // Tanggal: dari child paling awal sampai paling akhir
        $startDate = $children->whereNotNull('start_date')->min('start_date');
        $endDate   = $children->whereNotNull('end_date')->max('end_date');

        // Durasi: selisih hari kerja start & end
        $duration = null;
        if ($startDate && $endDate) {
            $duration = $this->countWorkingDays(
                \Carbon\Carbon::parse($startDate),
                \Carbon\Carbon::parse($endDate)
            );
        }

        $parent->update([
            'progress'   => round($avgProgress),
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'duration'   => $duration,
        ]);

        // Jika parent juga punya parent (Level 2 → Level 1)
        if ($parent->parent_id) {
            $this->recalculateParent($parent->parent);
        }
    }

    // ── Validasi Transisi Status ───────────────────────────

    private function validateTransition(string $from, string $to, User $by): void
    {
        $role = $by->getRoleName();

        $allowed = [
            'not_started' => ['in_progress'],
            'in_progress' => ['on_hold', 'done'],
            'on_hold'     => ['in_progress', 'cancelled'],
            'done'        => ['in_progress'], // hanya Manager
            'cancelled'   => ['not_started'], // hanya Manager
        ];

        if (!in_array($to, $allowed[$from] ?? [])) {
            throw new \Exception("Transisi status dari '{$from}' ke '{$to}' tidak diizinkan.");
        }

        // Reopen (done → in_progress) hanya Manager
        if ($from === 'done' && $to === 'in_progress' && $role !== 'Manager') {
            throw new \Exception('Hanya Manager yang dapat membuka kembali task yang sudah selesai.');
        }

        // Uncancel hanya Manager
        if ($from === 'cancelled' && $role !== 'Manager') {
            throw new \Exception('Hanya Manager yang dapat mengaktifkan kembali task yang dibatalkan.');
        }
    }

    // ── Hitung Hari Kerja ──────────────────────────────────

    public function countWorkingDays(\Carbon\Carbon $start, \Carbon\Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (\App\Models\WorkingCalendar::isWorkingDay($current)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    // ── Get untuk User ─────────────────────────────────────

    public function getForProject(Project $project, User $user)
    {
        return Task::visibleTo($user)
            ->where('project_id', $project->id)
            ->whereNull('parent_id') // hanya root task
            ->with(['children.children', 'assignments.user'])
            ->orderBy('created_at')
            ->get();
    }
}

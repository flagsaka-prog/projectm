<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Notification;

class NotificationService
{
    // ── Kirim notifikasi ke satu user ──────────────────────

    public function send(User $user, string $type, array $data): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'data'    => $data,
            'read_at' => null,
        ]);

        // Broadcast realtime via WebSocket (akan disetup saat Phase 2)
        // broadcast(new \App\Events\NotificationSent($notification))->toOthers();

        return $notification;
    }

    // ── Task baru di-assign ────────────────────────────────

    public function taskAssigned(Task $task, User $assignee): void
    {
        $this->send($assignee, 'task_assigned', [
            'task_id'      => $task->id,
            'task_name'    => $task->name,
            'project_id'   => $task->project_id,
            'project_name' => $task->project->name,
            'message'      => "Anda ditugaskan ke task: {$task->name}",
        ]);
    }

    // ── Proyek baru di-assign ke Manager ──────────────────

    public function projectAssigned(Project $project, User $manager): void
    {
        $this->send($manager, 'project_assigned', [
            'project_id'   => $project->id,
            'project_name' => $project->name,
            'message'      => "Anda ditunjuk sebagai PM proyek: {$project->name}",
        ]);
    }

    // ── Status task berubah ────────────────────────────────

    public function taskStatusChanged(Task $task, string $oldStatus, string $newStatus, User $changedBy): void
    {
        // Notifikasi ke creator task (Manager)
        if ($task->created_by !== $changedBy->id) {
            $creator = User::find($task->created_by);
            if ($creator) {
                $this->send($creator, 'task_status_changed', [
                    'task_id'    => $task->id,
                    'task_name'  => $task->name,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_by' => $changedBy->name,
                    'message'    => "Status task '{$task->name}' berubah dari {$oldStatus} ke {$newStatus}",
                ]);
            }
        }
    }

    // ── Deadline approaching (dipanggil via scheduled job) ─

    public function notifyDeadlineApproaching(Task $task): void
    {
        $assignments = $task->assignments()->with('user')->get();

        foreach ($assignments as $assignment) {
            $this->send($assignment->user, 'deadline_approaching', [
                'task_id'   => $task->id,
                'task_name' => $task->name,
                'end_date'  => $task->end_date?->toDateString(),
                'message'   => "Deadline task '{$task->name}' tinggal 2 hari lagi.",
            ]);
        }
    }

    // ── Mark as read ───────────────────────────────────────

    public function markAsRead(Notification $notification, User $user): void
    {
        // Pastikan notifikasi milik user ini
        if ($notification->user_id !== $user->id) {
            throw new \Exception('Unauthorized.');
        }

        $notification->markAsRead();
    }

    // ── Mark all as read ───────────────────────────────────

    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    // ── Unread count ───────────────────────────────────────

    public function unreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}

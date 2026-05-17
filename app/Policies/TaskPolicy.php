<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Siapa yang boleh lihat task
     * Executive/VP: semua task
     * PM: task di project yang dia kelola
     * Team Lead/Developer: task yang di-assign ke mereka
     */
    /**
     * Siapa yang boleh lihat task
     * Executive/VP: semua task
     * PM: task di project yang dia kelola
     * Team Lead/Developer: task yang di-assign, yang dia buat, atau subtask dari task yang dia pegang
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasAnyRole(['CEO', 'COO', 'CTO', 'CFO', 'VP'])) {
            return true;
        }

        if ($user->hasRole('PM')) {
            return $task->project->assigned_manager_id === $user->id;
        }

        if ($user->hasAnyRole(['Team Lead', 'Developer'])) {
            // 1. Jika dia di-assign langsung ke task ini
            if ($task->assignments()->where('user_id', $user->id)->exists()) {
                return true;
            }

            // 2. Jika dialah yang membuat task/subtask ini (created_by)
            if ($task->created_by === $user->id) {
                return true;
            }

            // 3. Jika dia di-assign ke Parent Task (Berlaku untuk Subtask)
            if ($task->parent_id) {
                $parentTask = $task->parent;
                if ($parentTask && $parentTask->assignments()->where('user_id', $user->id)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasAnyRole(['CEO', 'COO', 'VP', 'PM'])) {
            return true;
        }

        return false;
    }

    /**
     * Siapa yang boleh edit task
     * Executive/VP: semua
     * PM: task di project-nya
     * Team Lead/Developer: hanya task yang di-assign ke mereka
     */
    /**
     * Siapa yang boleh edit task
     * Executive/VP: semua
     * PM: task di project-nya
     * Team Lead/Developer: task yang di-assign, yang dia buat, atau subtask dari task yang dia pegang
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasAnyRole(['CEO', 'COO', 'CTO', 'CFO', 'VP'])) {
            return true;
        }

        if ($user->hasRole('PM')) {
            return $task->project->assigned_manager_id === $user->id;
        }

        if ($user->hasAnyRole(['Team Lead', 'Developer'])) {
            // 1. Jika dia di-assign langsung ke task ini
            if ($task->assignments()->where('user_id', $user->id)->exists()) {
                return true;
            }

            // 2. Jika dialah yang membuat task/subtask ini (created_by)
            if ($task->created_by === $user->id) {
                return true;
            }

            // 3. Jika dia di-assign ke Parent Task (Berlaku untuk Subtask)
            // Artinya: TL yang pegang Task Utama, otomatis boleh edit semua Subtask di bawahnya
            if ($task->parent_id) {
                $parentTask = $task->parent;
                if ($parentTask && $parentTask->assignments()->where('user_id', $user->id)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Siapa yang boleh hapus task
     * CEO/COO/VP/PM: task di project-nya
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->hasAnyRole(['CEO', 'COO', 'VP'])) {
            return true;
        }

        if ($user->hasRole('PM')) {
            return $task->project->assigned_manager_id === $user->id;
        }

        if ($user->hasAnyRole(['Team Lead', 'Developer'])) {
            // Izinkan hapus jika dia yang membuat atau dia pegang parent-nya
            if ($task->created_by === $user->id) {
                return true;
            }
            if ($task->parent_id) {
                $parentTask = $task->parent;
                if ($parentTask && $parentTask->assignments()->where('user_id', $user->id)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }
}

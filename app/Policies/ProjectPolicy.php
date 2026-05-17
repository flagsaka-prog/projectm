<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\TaskAssignment;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Siapa yang boleh lihat project
     * CEO/COO: semua project
     * CTO/CFO/VP: semua project (monitoring)
     * PM: project yang di-assign sebagai PM
     * Team Lead/Developer: project yang dia kerjakan (ada assignment)
     */
    public function view(User $user, Project $project): bool
    {
        // Executive level — lihat semua
        if ($user->hasAnyRole(['CEO', 'COO', 'CTO', 'CFO', 'VP'])) {
            return true;
        }

        // PM — project yang dia kelola
        if ($user->hasRole('PM')) {
            return $project->assigned_manager_id === $user->id;
        }

        // Team Lead/Developer — project yang ada task-nya
        if ($user->hasAnyRole(['Team Lead', 'Developer'])) {
            return TaskAssignment::where('user_id', $user->id)
                ->whereHas('task', fn($q) => $q->where('project_id', $project->id))
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['CEO', 'COO', 'VP']);
    }

    public function update(User $user, Project $project): bool
    {
        // CEO & COO: Bisa edit semua project
        if ($user->hasAnyRole(['CEO', 'COO'])) {
            return true;
        }

        // VP: Hanya boleh edit project yang dia buat sendiri
        if ($user->hasRole('VP')) {
            return $project->created_by === $user->id;
        }

        // PM: Bisa edit project yang di-assign kepadanya
        if ($user->hasRole('PM')) {
            return $project->assigned_manager_id === $user->id;
        }

        return false;
    }
    public function delete(User $user, Project $project): bool
    {
        // Hanya CEO, COO, atau VP (pembuat project) yang bisa hapus
        if ($user->hasAnyRole(['CEO', 'COO'])) {
            return true;
        }

        if ($user->hasRole('VP')) {
            return $project->created_by === $user->id;
        }

        return false;
    }
    public function archive(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}

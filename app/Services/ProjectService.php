<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    // ── Create ─────────────────────────────────────────────

    public function create(array $data, User $createdBy): Project
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $project = Project::create([
                'name'                => $data['name'],
                'description'         => $data['description'] ?? null,
                'created_by'          => $createdBy->id,
                'assigned_manager_id' => $data['assigned_manager_id'] ?? null,
                'start_date'          => $data['start_date'] ?? null,
                'end_date'            => $data['end_date'] ?? null,
                'status'              => 'planning',
                'budget'              => $data['budget'] ?? 0,
            ]);

            // Jika ada manager, daftarkan ke project_user
            if (!empty($data['assigned_manager_id'])) {
                $project->members()->attach($data['assigned_manager_id'], [
                    'role_in_project' => 'manager',
                ]);
            }

            return $project;
        });
    }

    // ── Update ─────────────────────────────────────────────

    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $oldManagerId = $project->assigned_manager_id;
            $newManagerId = $data['assigned_manager_id'] ?? null;

            $project->update([
                'name'                => $data['name'] ?? $project->name,
                'description'         => $data['description'] ?? $project->description,
                'assigned_manager_id' => $newManagerId,
                'start_date'          => $data['start_date'] ?? $project->start_date,
                'end_date'            => $data['end_date'] ?? $project->end_date,
                'status'              => $data['status'] ?? $project->status,
                'budget'              => $data['budget'] ?? $project->budget,
            ]);

            // Jika manager berubah, update project_user
            if ($oldManagerId !== $newManagerId) {
                // Lepas manager lama
                if ($oldManagerId) {
                    $project->members()->detach($oldManagerId);
                }
                // Daftarkan manager baru
                if ($newManagerId) {
                    $project->members()->attach($newManagerId, [
                        'role_in_project' => 'manager',
                    ]);
                }
            }

            return $project->fresh();
        });
    }

    // ── Archive ────────────────────────────────────────────

    public function archive(Project $project): Project
    {
        $project->update(['status' => 'archived']);
        return $project;
    }

    // ── Delete (soft) ──────────────────────────────────────

    public function delete(Project $project): void
    {
        $project->delete();
    }

    // ── Add Member ─────────────────────────────────────────

    public function addMember(Project $project, User $user, string $roleInProject = 'admin'): void
    {
        // Cegah duplikasi
        if (!$project->members()->where('users.id', $user->id)->exists()) {
            $project->members()->attach($user->id, [
                'role_in_project' => $roleInProject,
            ]);
        }
    }

    // ── Remove Member ──────────────────────────────────────

    public function removeMember(Project $project, User $user): void
    {
        $project->members()->detach($user->id);
    }

    // ── Recalculate Cost ───────────────────────────────────

    public function recalculateCost(Project $project): Project
    {
        $estimatedCost = $project->tasks()
            ->with('resources')
            ->get()
            ->sum(function ($task) {
                return $task->resources->sum('pivot.estimated_cost');
            });

        $actualCost = $project->tasks()
            ->with('resources')
            ->get()
            ->sum(function ($task) {
                return $task->resources->sum('pivot.actual_cost');
            });

        $project->update([
            'estimated_cost' => $estimatedCost,
            'actual_cost'    => $actualCost,
            'cost_variance'  => $estimatedCost - $actualCost,
        ]);

        return $project->fresh();
    }

    // ── Get for User ───────────────────────────────────────

    public function getForUser(User $user)
    {
        return Project::visibleTo($user)
            ->with(['manager', 'creator'])
            ->latest()
            ->get();
    }
}

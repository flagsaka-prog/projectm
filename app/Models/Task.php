<?php

namespace App\Models;

use App\Models\TaskDependency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TaskAssignment;
use App\Models\Project;
use App\Models\Timesheet;


class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_id',
        'level',
        'name',
        'description',
        'start_date',
        'end_date',
        'duration',
        'progress',
        'status',
        'created_by',
        'is_milestone', // <-- Tambahkan ini
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'level'      => 'integer',
        'progress'   => 'integer',
        'duration'   => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignments')
            ->withPivot('allocation_percent', 'planned_hours', 'actual_hours')
            ->withTimestamps();
    }

    public function dependencies()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function dependents()
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'task_resource')
            ->withPivot('allocation_percent', 'quantity', 'estimated_cost', 'actual_cost')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function baselines()
    {
        return $this->hasMany(TaskBaseline::class);
    }

    public function latestBaseline()
    {
        return $this->hasOne(TaskBaseline::class)->latestOfMany();
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }


    // ── Scopes ─────────────────────────────────────────────

    public function scopeVisibleTo($query, User $user)
    {
        $role = $user->roles->first();

        if (!$role) return $query->whereRaw('0=1');

        // Programmer & CEO: lihat semua
        if ($role->is_system || $role->name === 'CEO') {
            return $query;
        }

        // Manager: task dalam proyek yang dia pegang
        if ($role->name === 'Manager') {
            return $query->whereHas('project', function ($q) use ($user) {
                $q->where('assigned_manager_id', $user->id);
            });
        }

        // Admin: hanya task yang di-assign ke dia
        return $query->whereHas('assignments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // ── Helpers ────────────────────────────────────────────

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}

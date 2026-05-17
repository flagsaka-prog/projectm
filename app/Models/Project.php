<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'assigned_manager_id',
        'start_date',
        'end_date',
        'status',
        'budget',
        'estimated_cost',
        'actual_cost',
        'cost_variance',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'budget'         => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost'    => 'decimal:2',
        'cost_variance'  => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role_in_project')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function baselines()
    {
        return $this->hasMany(TaskBaseline::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeVisibleTo($query, User $user)
    {
        $role = $user->roles->first()->name ?? null;

        if (!$role) return $query->whereRaw('0=1');

        // Programmer: lihat semua
        if ($user->isProgrammer()) {
            return $query;
        }

        // Executive & VP: lihat semua project
        if (in_array($role, ['CEO', 'COO', 'CTO', 'CFO', 'VP'])) {
            return $query;
        }

        // PM: hanya project yang di-assign kepadanya
        if ($role === 'PM') {
            return $query->where('assigned_manager_id', $user->id);
        }

        // Team Lead & Developer: hanya project yang ada task-nya
        if (in_array($role, ['Team Lead', 'Developer'])) {
            return $query->whereHas('tasks', function ($q) use ($user) {
                $q->whereHas('assignments', fn($aq) => $aq->where('user_id', $user->id));
            });
        }

        return $query->whereRaw('0=1');
    }
}

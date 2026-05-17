<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use CausesActivity, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',
        'is_active'          => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role_in_project')
            ->withTimestamps();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'assigned_manager_id');
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments')->withPivot('allocation_percent');
    }

    public function session()
    {
        return $this->hasOne(UserSession::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeVisibleTo($query, User $user)
    {
        $role = $user->roles->first();

        if ($role && $role->is_system) {
            return $query;
        }

        return $query->whereHas('roles', function ($q) {
            $q->where('is_system', false);
        });
    }

    // ── Helpers ────────────────────────────────────────────

    public function getRoleName(): string
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    public function isOnline(): bool
    {
        // Cek cache, jika ada = online, jika tidak = offline
        return Cache::has('user-is-online-' . $this->id);
    }

    public function isProgrammer(): bool
    {
        return $this->roles->first()?->is_system ?? false;
    }
}

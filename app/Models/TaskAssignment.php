<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'allocation_percent',
        'planned_hours',
        'actual_hours',
    ];

    protected $casts = [
        'allocation_percent' => 'integer',
        'planned_hours'      => 'decimal:2',
        'actual_hours'       => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

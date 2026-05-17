<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'material_unit',
        'initials',
        'group',
        'max_units',
        'std_rate',
        'ovt_rate',
        'cost_per_use',
        'accrue_at',
    ];

    protected $casts = [
        'max_units'    => 'decimal:2',
        'std_rate'     => 'decimal:2',
        'ovt_rate'     => 'decimal:2',
        'cost_per_use' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_resource')
            ->withPivot('allocation_percent', 'quantity', 'estimated_cost', 'actual_cost')
            ->withTimestamps();
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeHuman($query)
    {
        return $query->where('type', 'human');
    }

    public function scopeEquipment($query)
    {
        return $query->where('type', 'equipment');
    }

    public function scopeMaterial($query)
    {
        return $query->where('type', 'material');
    }
}

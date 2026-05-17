<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'level',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'level'     => 'integer',
    ];

    // Scope: hanya role bisnis (bukan system)
    public function scopeBusiness($query)
    {
        return $query->where('is_system', false);
    }
}

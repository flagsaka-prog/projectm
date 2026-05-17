<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskBaseline extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'task_name',
        'start_date',
        'end_date',
        'duration',
        'estimated_cost',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'estimated_cost' => 'decimal:2',
    ];
}

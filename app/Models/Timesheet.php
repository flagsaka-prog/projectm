<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'work_date',
        'hours_worked',
        'description',
    ];

    protected $casts = [
        'work_date'    => 'date',
        'hours_worked' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

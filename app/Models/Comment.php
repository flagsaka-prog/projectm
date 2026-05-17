<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'body',
        'parent_id',
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

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    // Hanya komentar utama (bukan reply)
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}

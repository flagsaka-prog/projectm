<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'filename',
        'filepath',
        'size',
        'mime_type',
    ];

    protected $casts = [
        'size' => 'integer',
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

    // ── Helpers ────────────────────────────────────────────

    // URL untuk download
    public function getUrlAttribute(): string
    {
        return Storage::url($this->filepath);
    }

    // Ukuran file dalam format readable
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'content',
        'attachment_path',
        'attachment_original_name',
        'grade',
        'grade_comment',
        'graded_at',
        'submitted_at',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'graded_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(SubmissionHistory::class)->orderBy('submitted_at', 'desc');
    }

    public function attachmentPublicUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function deleteStoredAttachment(): void
    {
        if ($this->attachment_path && Storage::disk('public')->exists($this->attachment_path)) {
            Storage::disk('public')->delete($this->attachment_path);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Submission $submission) {
            $submission->deleteStoredAttachment();
        });
    }
}

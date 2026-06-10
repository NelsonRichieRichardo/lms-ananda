<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'course_id',
    'title',
    'content',
    'attachment_path',
    'attachment_original_name',
    'order_position',
])]
class Module extends Model
{
    protected function casts(): array
    {
        return [
            'order_position' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Module $module): void {
            $module->deleteStoredAttachment();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ModuleProgress::class);
    }

    public function deleteStoredAttachment(): void
    {
        if ($this->attachment_path) {
            Storage::disk('public')->delete($this->attachment_path);
        }
    }

    public function attachmentPublicUrl(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }
}

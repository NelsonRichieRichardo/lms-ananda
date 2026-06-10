<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'slug',
    'description',
    'cover_image',
    'teacher_id',
    'is_published',
])]
class Course extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order_position');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('order_position');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class);
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Public URL for cover: supports legacy full URLs or a storage path on the public disk.
     */
    public function coverPublicUrl(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (preg_match('#^https?://#i', $this->cover_image)) {
            return $this->cover_image;
        }

        return Storage::disk('public')->url($this->cover_image);
    }
}

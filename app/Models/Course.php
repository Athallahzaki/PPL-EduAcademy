<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable attributes.
     * Note: instructor_id and status MUST NOT be mass assignable to prevent privilege escalation.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'level',
        'banner_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'level' => CourseLevel::class,
        ];
    }

    /**
     * Course owner / instructor.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Course subject / category.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Direct enrollment records.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Student reviews for this course.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * Modules belonging to this course, ordered by position.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('position');
    }

    /**
     * Chapters belonging to this course through modules.
     */
    public function chapters(): HasManyThrough
    {
        return $this->hasManyThrough(Chapter::class, Module::class);
    }

    /**
     * Enrolled student users.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withPivot('enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Resolved URL for banner image on the public disk.
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->banner_path ? Storage::disk('public')->url($this->banner_path) : null,
        );
    }

    /**
     * Scope for published courses.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published->value);
    }

    /**
     * Scope for courses visible in the public/student catalog.
     * Published AND instructor is active and not soft-deleted.
     */
    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published->value)
            ->whereHas('instructor', function (Builder $q) {
                $q->where('status', AccountStatus::Active->value);
            });
    }

    /**
     * Scope for classes owned by a specific instructor.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        $id = $user instanceof User ? $user->id : $user;

        return $query->where('instructor_id', $id);
    }

    /**
     * Search courses by title with escaped wildcards.
     */
    public function scopeSearch(Builder $query, ?string $q): Builder
    {
        if (blank($q)) {
            return $query;
        }

        $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($q));

        return $query->where('title', 'like', "%{$term}%");
    }

    /**
     * Check if a course is currently open for enrollment.
     */
    public function isEnrollable(): bool
    {
        return $this->status === CourseStatus::Published
            && $this->instructor !== null
            && $this->instructor->status === AccountStatus::Active
            && ! $this->instructor->trashed();
    }
}


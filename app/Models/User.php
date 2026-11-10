<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * role, status, must_change_password, and onboarded_at are intentionally omitted
     * to prevent mass-assignment privilege escalation.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Bootstrap model events.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->email !== null) {
                $user->email = strtolower(trim($user->email));
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'status' => AccountStatus::class,
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'onboarded_at' => 'datetime',
        ];
    }

    /**
     * Instructor profile relation (one-to-one).
     */
    public function instructorProfile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Check if user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === Role::Instructor;
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === AccountStatus::Suspended;
    }

    /**
     * Check if user needs onboarding.
     * Admins never need onboarding.
     */
    public function needsOnboarding(): bool
    {
        return $this->role !== Role::Admin && $this->onboarded_at === null;
    }

    /**
     * Get the role's dashboard route name.
     */
    public function dashboardRoute(): string
    {
        return $this->role?->dashboardRoute() ?? 'login';
    }

    /**
     * Filter by role.
     */
    public function scopeWithRole(Builder $query, Role|string|null $role): Builder
    {
        if (empty($role)) {
            return $query;
        }

        $value = $role instanceof Role ? $role->value : $role;
        return $query->where('role', $value);
    }

    /**
     * Filter by status.
     */
    public function scopeWithStatus(Builder $query, AccountStatus|string|null $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        $value = $status instanceof AccountStatus ? $status->value : $status;
        return $query->where('status', $value);
    }

    /**
     * Search by name or email.
     */
    public function scopeSearch(Builder $query, ?string $q): Builder
    {
        if (blank($q)) {
            return $query;
        }

        $term = '%' . trim($q) . '%';
        return $query->where(function (Builder $subQuery) use ($term) {
            $subQuery->where('name', 'like', $term)
                ->orWhere('email', 'like', $term);
        });
    }

    /**
     * Total count of active admins.
     */
    public static function activeAdminCount(): int
    {
        return static::where('role', Role::Admin->value)
            ->where('status', AccountStatus::Active->value)
            ->count();
    }
}

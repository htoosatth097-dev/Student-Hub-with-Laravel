<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_STUDENT_ADMIN = 'student_admin';
    public const ROLE_COURSE_ADMIN = 'course_admin';
    public const ROLE_USER = 'user';

    /** @var list<string> */
    public const ADMIN_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_STUDENT_ADMIN,
        self::ROLE_COURSE_ADMIN,
    ];

    /** @var list<string> */
    public const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_STUDENT_ADMIN,
        self::ROLE_COURSE_ADMIN,
        self::ROLE_USER,
    ];

    /** @var array<string, string> */
    public const ROLE_LABELS = [
        self::ROLE_SUPER_ADMIN   => 'Super Admin',
        self::ROLE_STUDENT_ADMIN => 'Student Admin',
        self::ROLE_COURSE_ADMIN  => 'Course Admin',
        self::ROLE_USER          => 'Standard User',
    ];

    /**
     * Role is deliberately not mass assignable. Role changes must be made by
     * trusted server-side code, such as the make:admin Artisan command.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isStudentAdmin(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT_ADMIN);
    }

    public function isCourseAdmin(): bool
    {
        return $this->hasRole(self::ROLE_COURSE_ADMIN);
    }

    /** Return true for any of the three administrative roles. */
    public function isAdmin(): bool
    {
        return in_array($this->role, self::ADMIN_ROLES, true);
    }

    public function canManageStudents(): bool
    {
        return $this->isSuperAdmin() || $this->isStudentAdmin();
    }

    public function canManageCourses(): bool
    {
        return $this->isSuperAdmin() || $this->isCourseAdmin();
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? 'Standard User';
    }

    public function roleBadgeClass(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN   => 'role-pill-danger',
            self::ROLE_STUDENT_ADMIN => 'role-pill-teal',
            self::ROLE_COURSE_ADMIN  => 'role-pill-indigo',
            default                  => 'role-pill-slate',
        };
    }

    /** Compute 2-character uppercase initials from the user's name. */
    public function initials(string $fallback = 'U'): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $initials = '';

        if (! empty($parts[0])) {
            $initials .= mb_substr($parts[0], 0, 1);
        }
        if (! empty($parts[1])) {
            $initials .= mb_substr($parts[1], 0, 1);
        }

        return strtoupper($initials !== '' ? $initials : $fallback);
    }

    /** Path the signed-in user should land on after login. */
    public function landingPath(): string
    {
        return match (true) {
            $this->isStudentAdmin()    => '/students/create',
            $this->canManageStudents() => '/students',
            $this->canManageCourses()  => '/courses',
            default                    => '/about',
        };
    }

    /**
     * Scope a query to filter users by search term (name/email) and role.
     *
     * @param  Builder<self>  $query
     * @param  array{search?: ?string, role?: ?string}  $filters
     * @return Builder<self>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $role = trim((string) ($filters['role'] ?? ''));
        if ($role !== '') {
            $query->where('role', $role);
        }

        return $query;
    }

    /**
     * Retrieve user counts grouped by role in a single query.
     *
     * @return array<string, int>
     */
    public static function roleCounts(): array
    {
        $rawCounts = self::query()
            ->selectRaw('role, COUNT(*) as aggregate')
            ->groupBy('role')
            ->pluck('aggregate', 'role');

        $counts = [];
        foreach (self::ROLES as $role) {
            $counts[$role] = (int) ($rawCounts[$role] ?? 0);
        }

        return $counts;
    }
}

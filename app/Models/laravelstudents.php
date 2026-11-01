<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class laravelstudents extends Model
{
    protected $table = 'laravel_students';

    /** @var list<string> */
    public const GENDERS = ['Male', 'Female', 'Other'];

    /** @var array<string, string> */
    public const SORT_OPTIONS = [
        'newest'    => 'Newest first',
        'oldest'    => 'Oldest first',
        'name_asc'  => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'code_asc'  => 'Student code A-Z',
    ];

    protected $fillable = [
        'student_code',
        'student_name',
        'email',
        'phone_number',
        'gender',
        'course_id',
    ];

    /**
     * A student belongs to one course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(courses::class, 'course_id');
    }

    /**
     * Compute 2-character uppercase initials from the student's name.
     */
    public function initials(string $fallback = 'S'): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->student_name)) ?: [];
        $initials = '';

        if (! empty($parts[0])) {
            $initials .= mb_substr($parts[0], 0, 1);
        }
        if (! empty($parts[1])) {
            $initials .= mb_substr($parts[1], 0, 1);
        }

        return strtoupper($initials !== '' ? $initials : $fallback);
    }

    /**
     * Apply directory search, course filter, gender filter, and sort order.
     *
     * @param  Builder<self>  $query
     * @param  array{search?: ?string, q?: ?string, course_id?: mixed, gender?: ?string, sort?: ?string}  $filters
     * @return Builder<self>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $term = trim((string) (($filters['search'] ?? '') !== '' ? $filters['search'] : ($filters['q'] ?? '')));

        if ($term !== '') {
            $query->where(function (Builder $q) use ($term): void {
                $q->where('student_name', 'like', '%'.$term.'%')
                    ->orWhere('student_code', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
        }

        if (! empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        return match ($filters['sort'] ?? 'newest') {
            'oldest'    => $query->orderBy('id'),
            'name_asc'  => $query->orderBy('student_name'),
            'name_desc' => $query->orderByDesc('student_name'),
            'code_asc'  => $query->orderBy('student_code'),
            default     => $query->orderByDesc('id'),
        };
    }

    /**
     * Generate the next unique STU-XXX code starting from the given student ID.
     */
    public static function generateNextCode(int $seedId): string
    {
        $number = max(1, $seedId);
        do {
            $studentCode = 'STU-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
            $number++;
        } while (self::where('student_code', $studentCode)->exists());

        return $studentCode;
    }

    /**
     * Compute full registry statistics for the dashboard.
     *
     * @return array{
     *     totalCount: int,
     *     maleCount: int,
     *     femaleCount: int,
     *     otherCount: int,
     *     uniqueCoursesCount: int,
     *     byCourse: \Illuminate\Support\Collection<string, int>
     * }
     */
    public static function dashboardStats(): array
    {
        $allStudents = self::with('course:id,course_name')->get();
        $totalCount = $allStudents->count();
        $maleCount = $allStudents->where('gender', 'Male')->count();
        $femaleCount = $allStudents->where('gender', 'Female')->count();
        $otherCount = max(0, $totalCount - $maleCount - $femaleCount);

        $byCourse = $allStudents
            ->groupBy(fn (self $s) => $s->course?->course_name ?? 'General')
            ->map->count()
            ->sortDesc();

        return [
            'totalCount'         => $totalCount,
            'maleCount'          => $maleCount,
            'femaleCount'        => $femaleCount,
            'otherCount'         => $otherCount,
            'uniqueCoursesCount' => $allStudents->pluck('course.course_name')->filter()->unique()->count(),
            'byCourse'           => $byCourse,
        ];
    }
}

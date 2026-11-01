<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class courses extends Model
{
    protected $table = 'courses';

    protected $fillable = [
        'course_name',
        'description',
        'course_code',
        'duration_hours',
        'course_fee',
    ];

    /**
     * A course has many students.
     */
    public function students(): HasMany
    {
        return $this->hasMany(laravelstudents::class, 'course_id');
    }

    /**
     * Return clean formatted duration (e.g. "120 hrs") without duplicate "hrs/hours" suffixes.
     */
    public function formattedDuration(): ?string
    {
        if (blank($this->duration_hours)) {
            return null;
        }

        $cleaned = preg_replace('/(?:\s*(?:hrs?|hours?))+$/i', '', trim((string) $this->duration_hours));

        return ($cleaned !== '' ? $cleaned : trim((string) $this->duration_hours)).' hrs';
    }

    /**
     * Return formatted tuition fee in MMK (e.g. "150,000 MMK").
     */
    public function formattedFee(): ?string
    {
        if (blank($this->course_fee)) {
            return null;
        }

        $numeric = preg_replace('/[^\d.]/', '', (string) $this->course_fee);
        $formatted = ($numeric !== '' && is_numeric($numeric) && (float) $numeric > 0)
            ? number_format((float) $numeric)
            : trim((string) $this->course_fee);

        return $formatted.' MMK';
    }

    /**
     * Uppercase first letter for course badges.
     */
    public function badgeLetter(): string
    {
        $trimmed = trim((string) $this->course_name);

        return $trimmed !== '' ? strtoupper(mb_substr($trimmed, 0, 1)) : 'C';
    }

    /**
     * Generate the next sequential course code (e.g. CS-101, CS-102).
     */
    public static function nextCourseCode(): string
    {
        $lastCodeNumber = self::query()
            ->lockForUpdate()
            ->pluck('course_code')
            ->map(static function (?string $code): ?int {
                return preg_match('/^CS-(\d+)$/', $code ?? '', $matches)
                    ? (int) $matches[1]
                    : null;
            })
            ->filter(static fn (?int $number): bool => $number !== null)
            ->max();

        return sprintf('CS-%03d', max(100, (int) $lastCodeNumber) + 1);
    }
}

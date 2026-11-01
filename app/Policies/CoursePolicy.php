<?php

namespace App\Policies;

use App\Models\User;
use App\Models\courses;

/** Authorization rules for the course catalog and course management. */
class CoursePolicy
{
    /** Every signed-in account may read the catalog; it contains no student PII. */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, User::ROLES, true);
    }

    public function view(User $user, courses $course): bool
    {
        return in_array($user->role, User::ROLES, true);
    }

    public function create(User $user): bool
    {
        return $user->canManageCourses();
    }

    public function update(User $user, courses $course): bool
    {
        return $user->canManageCourses();
    }

    public function delete(User $user, courses $course): bool
    {
        return $user->canManageCourses();
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\laravelstudents;

/** Authorization rules for student records. */
class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageStudents();
    }

    public function view(User $user, laravelstudents $student): bool
    {
        return $user->canManageStudents();
    }

    public function create(User $user): bool
    {
        return $user->canManageStudents();
    }

    public function update(User $user, laravelstudents $student): bool
    {
        return $user->canManageStudents();
    }

    public function delete(User $user, laravelstudents $student): bool
    {
        return $user->canManageStudents();
    }

    public function export(User $user): bool
    {
        return $user->canManageStudents();
    }
}

<?php

namespace App\Providers;

use App\Models\courses;
use App\Models\laravelstudents;
use App\Models\User;
use App\Policies\CoursePolicy;
use App\Policies\StudentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // System-wide administration is reserved for the Super Admin.
        Gate::define('manage-users', static fn (User $user): bool => $user->isSuperAdmin());
        Gate::define('manage-roles', static fn (User $user): bool => $user->isSuperAdmin());

        Gate::policy(laravelstudents::class, StudentPolicy::class);
        Gate::policy(courses::class, CoursePolicy::class);
    }
}

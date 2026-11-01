# StudentHub Authorization Guide (Laravel 12)

## Roles

- `super_admin`: can manage students and courses; only role allowed to use the `manage-users` and `manage-roles` Gates (the actual user/role administration screens are not implemented in this project).
- `student_admin`: can view, create, update, and delete student records; can view the course catalog but cannot create, edit, or delete courses.
- `course_admin`: can view, create, update, and delete courses; cannot access student records.
- `user`: can view the course catalog and public About/Contact pages; cannot manage records.

## Authorization architecture

- `auth` middleware verifies the user is signed in.
- `StudentPolicy` protects every student CRUD controller action.
- Student CSV export visibility uses the `export` ability; a future server-side export route must call `$this->authorize('export', laravelstudents::class)`.
- `CoursePolicy` makes the course catalog read-only for standard users and Student Admins, and grants course CRUD to Super Admins and Course Admins.
- `manage-users` and `manage-roles` are reserved for Super Admin functionality; user/role CRUD UI is not implemented yet.
- Blade `@can` checks hide management links and forms. These checks improve the interface but are not a substitute for server-side Policies.
- Role is intentionally excluded from `User::$fillable`. Trusted role creation uses `php artisan make:admin --role=...`.
- Logout is available only through the POST `logout` route.

## Create privileged accounts

Omit `--password` to be prompted for a password without putting it directly in your shell history.

```bash
php artisan make:admin --name="System Owner" --email="owner@example.com" --role=super_admin
php artisan make:admin --name="Student Manager" --email="students@example.com" --role=student_admin
php artisan make:admin --name="Course Manager" --email="courses@example.com" --role=course_admin
```

Create a standard user account with `php artisan make:user --name="Catalog Reader" --email="reader@example.com"`; omit `--password` to enter it securely in the prompt. This command never accepts a role option and always creates a standard user.

The legacy `admin` database role is converted to `super_admin` by migration `2026_10_20_000001_normalize_legacy_admin_roles`.

## Local demo accounts

For local role testing only, seed four demo accounts with:

```bash
php artisan db:seed --class=DemoRoleUsersSeeder
```

The seeder refuses to run outside `local` or `testing`. All four accounts use the password `StudentHubDemo!2026`:

| Role | Email |
|---|---|
| `super_admin` | `superadmin@studenthub.local` |
| `student_admin` | `studentadmin@studenthub.local` |
| `course_admin` | `courseadmin@studenthub.local` |
| `user` | `user@studenthub.local` |

These credentials are for local testing only; do not use them in production. The role authorization test seeds these accounts automatically and fakes the reCAPTCHA response.

## Setup and verification

```bash
composer install
cp .env.example .env
php artisan key:generate
# Set DB / reCAPTCHA values in .env before using login.
php artisan migrate
php artisan db:seed
php artisan make:admin --name="System Owner" --email="owner@example.com" --role=super_admin
php artisan make:user --name="Catalog Reader" --email="reader@example.com"
php artisan route:list
php artisan test
```

The login form retains the existing Google reCAPTCHA requirement. The public Google test keys in `.env.example` are for local use only; configure real `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` values for production. Production rejects Google's test secret keys.

## Key files

- `app/Models/User.php`: role constants, role predicates, module permissions and role-specific landing route.
- `app/Policies/StudentPolicy.php`: Student CRUD permissions.
- `app/Policies/CoursePolicy.php`: Course catalog and Course CRUD permissions.
- `app/Providers/AppServiceProvider.php`: Gate and Policy registrations.
- `routes/web.php`: authenticated routes; authorization is enforced in controllers by Policies.
- `app/Http/Controllers/CourseController.php`: Course create/update/delete and protection against deleting a course that still has enrolled students.
- `app/Console/Commands/CreateAdminCommand.php`: secure role-aware admin account creation.
- `app/Console/Commands/CreateUserCommand.php`: creates standard users without accepting an externally supplied role.
- `database/seeders/DemoRoleUsersSeeder.php`: local/testing-only accounts for manually checking each role.
- `database/migrations/2026_10_20_000001_normalize_legacy_admin_roles.php`: converts existing `admin` roles to `super_admin`.
- `tests/Feature/AdminAuthTest.php`: role separation and authorization coverage.
- `tests/Feature/ExampleTest.php`: student/course workflow coverage.

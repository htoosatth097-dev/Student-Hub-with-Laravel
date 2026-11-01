<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateAdminCommand extends Command
{
    protected $signature = 'make:admin
                            {--name= : The account holder full name}
                            {--email= : The account email address}
                            {--password= : The account password}
                            {--role=super_admin : super_admin, student_admin, or course_admin}
                            {--force : Update an existing account and its role/password}';

    protected $description = 'Create a StudentHub administrator with a specific role';

    public function handle(): int
    {
        $this->info('=============================================');
        $this->info(' StudentHub — Administrator Account Creation ');
        $this->info('=============================================');

        $name = $this->option('name') ?: $this->ask('Enter full name', 'Administrator');
        $email = $this->option('email') ?: $this->ask('Enter email address');
        $role = strtolower(trim((string) $this->option('role')));

        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret('Enter password (min 8 characters)');
            $confirmPassword = $this->secret('Confirm password');

            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match. Account creation aborted.');
                return self::FAILURE;
            }
        }

        $emailRule = ['required', 'string', 'email', 'max:255'];
        if (! $this->option('force')) {
            $emailRule[] = 'unique:users,email';
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => strtolower(trim((string) $email)),
            'password' => $password,
            'role' => $role,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRule,
            'password' => ['required', 'string', Password::min(8)],
            'role' => ['required', 'string', Rule::in(User::ADMIN_ROLES)],
        ]);

        if ($validator->fails()) {
            $this->error('Validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->line('  - '.$error);
            }
            return self::FAILURE;
        }

        $normalizedEmail = strtolower(trim((string) $email));
        $admin = User::firstOrNew(['email' => $normalizedEmail]);
        $admin->name = $name;
        $admin->password = Hash::make((string) $password);
        $admin->email_verified_at = now();
        $admin->forceFill(['role' => $role])->save();

        $this->newLine();
        $this->info('Administrator account created/updated successfully.');
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Updated At'],
            [[$admin->id, $admin->name, $admin->email, $admin->roleLabel(), $admin->updated_at->toDateTimeString()]]
        );

        return self::SUCCESS;
    }
}

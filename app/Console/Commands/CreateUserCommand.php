<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    protected $signature = 'make:user
                            {--name= : The account holder full name}
                            {--email= : The account email address}
                            {--password= : The account password}';

    protected $description = 'Create a standard StudentHub user account';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Enter full name', 'StudentHub User');
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Enter email address'))));
        $password = $this->option('password');

        if (! $password) {
            $password = $this->secret('Enter password (min 8 characters)');
            $confirmation = $this->secret('Confirm password');

            if ($password !== $confirmation) {
                $this->error('Passwords do not match. Account creation aborted.');
                return self::FAILURE;
            }
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        if ($validator->fails()) {
            $this->error('Validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->line('  - '.$error);
            }
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make((string) $password),
        ]);
        // Never accept a role value from command/request input for this command.
        $user->forceFill(['role' => User::ROLE_USER])->save();

        $this->info('Standard user account created successfully.');
        $this->table(['ID', 'Name', 'Email', 'Role'], [[
            $user->id,
            $user->name,
            $user->email,
            $user->roleLabel(),
        ]]);

        return self::SUCCESS;
    }
}

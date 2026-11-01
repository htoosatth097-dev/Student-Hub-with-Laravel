<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoRoleUsersSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'StudentHubDemo!2026';

    /** @var array<string, string> */
    public const ACCOUNTS = [
        'super_admin' => 'superadmin@studenthub.local',
        'student_admin' => 'studentadmin@studenthub.local',
        'course_admin' => 'courseadmin@studenthub.local',
        'user' => 'user@studenthub.local',
    ];

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo role accounts may only be seeded in local or testing environments.');
        }

        foreach (self::ACCOUNTS as $role => $email) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => str($role)->replace('_', ' ')->title().' Demo',
                    'password' => Hash::make(self::DEMO_PASSWORD),
                    'email_verified_at' => now(),
                ]
            );

            $user->forceFill(['role' => $role])->save();
        }
    }
}

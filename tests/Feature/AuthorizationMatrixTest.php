<?php

namespace Tests\Feature;

use App\Models\courses;
use App\Models\laravelstudents;
use App\Models\User;
use Database\Seeders\AcademicDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationMatrixTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function validStudentData(int $courseId, string $tag, int $phoneSuffix): array
    {
        return [
            'student_name' => 'Authorization '.$tag,
            'email' => 'authorization-'.$tag.'@example.com',
            'phone_number' => sprintf('0912345%04d', $phoneSuffix),
            'gender' => 'Male',
            'course_id' => $courseId,
        ];
    }

    public function test_guest_requests_to_student_and_course_routes_redirect_to_login(): void
    {
        $this->seed(AcademicDataSeeder::class);
        $student = laravelstudents::firstOrFail();
        $course = courses::firstOrFail();

        $requests = [
            fn () => $this->get('/students'),
            fn () => $this->get('/students/create'),
            fn () => $this->post('/students', []),
            fn () => $this->get('/students/'.$student->id),
            fn () => $this->get('/students/'.$student->id.'/edit'),
            fn () => $this->put('/students/'.$student->id, []),
            fn () => $this->patch('/students/'.$student->id, []),
            fn () => $this->delete('/students/'.$student->id),
            fn () => $this->get('/courses'),
            fn () => $this->post('/courses', []),
            fn () => $this->get('/courses/'.$course->id.'/edit'),
            fn () => $this->put('/courses/'.$course->id, []),
            fn () => $this->patch('/courses/'.$course->id, []),
            fn () => $this->delete('/courses/'.$course->id),
        ];

        foreach ($requests as $request) {
            $request()->assertRedirect(route('login'));
        }
    }

    public function test_student_routes_enforce_permissions_for_every_role(): void
    {
        $this->seed(AcademicDataSeeder::class);
        $course = courses::firstOrFail();
        $roles = [
            ['super_admin', User::factory()->superAdmin()->create(), true],
            ['student_admin', User::factory()->studentAdmin()->create(), true],
            ['course_admin', User::factory()->courseAdmin()->create(), false],
            ['user', User::factory()->create(), false],
        ];

        foreach ($roles as $index => [$role, $user, $canManageStudents]) {
            $student = laravelstudents::firstOrFail();
            $expectedStatus = $canManageStudents ? 200 : 403;
            $phoneSuffix = $index + 1;
            $assertWriteResponse = fn ($response) => $canManageStudents
                ? $response->assertRedirect(route('students.index'))
                : $response->assertForbidden();

            $this->actingAs($user)->get('/students')->assertStatus($expectedStatus);
            $this->actingAs($user)->get('/students/create')->assertStatus($expectedStatus);
            $this->actingAs($user)->get('/students/'.$student->id)->assertStatus($expectedStatus);
            $this->actingAs($user)->get('/students/'.$student->id.'/edit')->assertStatus($expectedStatus);

            $assertWriteResponse($this->actingAs($user)->post(
                '/students',
                $this->validStudentData($course->id, $role, $phoneSuffix)
            ));

            $updateData = [
                'student_name' => 'Updated '.$role,
                'email' => 'updated-'.$role.'@example.com',
                'phone_number' => sprintf('0912346%04d', $phoneSuffix),
                'gender' => 'Female',
                'course_id' => $course->id,
            ];
            $assertWriteResponse($this->actingAs($user)->put('/students/'.$student->id, $updateData));
            $assertWriteResponse($this->actingAs($user)->patch('/students/'.$student->id, $updateData));
            $assertWriteResponse($this->actingAs($user)->delete('/students/'.$student->id));
        }
    }

    public function test_course_routes_enforce_permissions_for_every_role(): void
    {
        $roles = [
            ['super_admin', User::factory()->superAdmin()->create(), true],
            ['student_admin', User::factory()->studentAdmin()->create(), false],
            ['course_admin', User::factory()->courseAdmin()->create(), true],
            ['user', User::factory()->create(), false],
        ];

        foreach ($roles as [$role, $user, $canManageCourses]) {
            $course = courses::create(['course_name' => 'Authorization '.$role]);

            $this->actingAs($user)->get('/courses')->assertOk();
            $assertWriteResponse = fn ($response) => $canManageCourses
                ? $response->assertRedirect(route('courses.index'))
                : $response->assertForbidden();

            $assertWriteResponse($this->actingAs($user)->post('/courses', [
                'course_name' => 'Created '.$role,
            ]));

            $this->actingAs($user)->get('/courses/'.$course->id.'/edit')
                ->assertStatus($canManageCourses ? 200 : 403);

            $assertWriteResponse($this->actingAs($user)->put('/courses/'.$course->id, [
                'course_name' => 'Updated '.$role,
            ]));

            $assertWriteResponse($this->actingAs($user)->patch('/courses/'.$course->id, [
                'course_name' => 'Patched '.$role,
            ]));

            $assertWriteResponse($this->actingAs($user)->delete('/courses/'.$course->id));
        }
    }

    public function test_export_button_is_visible_only_to_roles_allowed_to_manage_students(): void
    {
        $this->seed(AcademicDataSeeder::class);

        foreach ([
            User::factory()->superAdmin()->create(),
            User::factory()->studentAdmin()->create(),
        ] as $user) {
            $this->actingAs($user)->get('/students')
                ->assertOk()
                ->assertSee('id="exportCsvBtn"', false);
        }

        foreach ([
            User::factory()->courseAdmin()->create(),
            User::factory()->create(),
        ] as $user) {
            $this->actingAs($user)->get('/students')->assertForbidden();
        }
    }
}

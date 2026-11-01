<?php

namespace Tests\Feature;

use App\Models\courses;
use App\Models\laravelstudents;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
    }

    private function makeCourse(string $name = 'Laravel'): courses
    {
        return courses::create([
            'course_name' => $name,
            'description' => 'Backend course',
            'course_code' => 'LV-101',
            'duration_hours' => '40',
            'course_fee' => '150000',
        ]);
    }

    private function validStudentData(int $courseId, array $override = []): array
    {
        return array_merge([
            'student_code' => 'STU-001',
            'student_name' => 'Mg Mg',
            'email' => 'mgmg@example.com',
            'phone_number' => '09123456789',
            'gender' => 'Male',
            'course_id' => $courseId,
        ], $override);
    }

    public function test_root_redirects_each_authenticated_admin_to_their_module(): void
    {
        $this->get('/')->assertRedirect('/students');
    }

    public function test_student_directory_page_loads_successfully(): void
    {
        $response = $this->get('/students');
        $response->assertStatus(200);
        $response->assertSee('Student Directory');
    }

    public function test_student_directory_lists_students_with_their_course_name(): void
    {
        $course = $this->makeCourse('Laravel Web Development');
        laravelstudents::create($this->validStudentData($course->id));

        $this->get('/students')
            ->assertStatus(200)
            ->assertSee('Mg Mg')
            ->assertSee('Laravel Web Development');
    }

    public function test_student_create_page_loads_with_course_dropdown(): void
    {
        $this->makeCourse('Laravel Web Development');

        $this->get('/students/create')
            ->assertStatus(200)
            ->assertSee('Add New Student')
            ->assertSee('Laravel Web Development');
    }

    public function test_student_can_be_created(): void
    {
        $course = $this->makeCourse();
        $data = $this->validStudentData($course->id);
        unset($data['student_code']);

        $this->post('/students', $data)
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('laravel_students', [
            'student_code' => 'STU-001',
            'student_name' => 'Mg Mg',
            'phone_number' => '09123456789',
            'course_id' => $course->id,
        ]);
    }

    public function test_student_codes_are_auto_incremented_and_submitted_codes_are_ignored(): void
    {
        $course = $this->makeCourse();
        $first = $this->validStudentData($course->id);
        unset($first['student_code']);

        $this->post('/students', $first)->assertRedirect(route('students.index'));

        $second = $this->validStudentData($course->id, [
            'email' => 'second@example.com',
            'phone_number' => '09123456780',
            'student_code' => 'MY-CUSTOM-CODE',
        ]);
        $this->post('/students', $second)->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('laravel_students', ['student_code' => 'STU-001']);
        $this->assertDatabaseHas('laravel_students', ['student_code' => 'STU-002']);
        $this->assertDatabaseMissing('laravel_students', ['student_code' => 'MY-CUSTOM-CODE']);
    }

    public function test_student_creation_validates_required_fields(): void
    {
        $this->post('/students', [])
            ->assertSessionHasErrors(['student_name', 'email', 'phone_number', 'gender', 'course_id']);
    }

    public function test_student_creation_rejects_unknown_course_and_non_numeric_phone(): void
    {
        $this->post('/students', $this->validStudentData(999, ['phone_number' => 'abc']))
            ->assertSessionHasErrors(['course_id', 'phone_number']);
    }

    public function test_student_code_email_and_phone_must_be_unique(): void
    {
        $course = $this->makeCourse();
        laravelstudents::create($this->validStudentData($course->id));

        $this->post('/students', $this->validStudentData($course->id))
            ->assertSessionHasErrors(['email', 'phone_number']);
    }

    public function test_student_show_and_edit_pages_load_successfully(): void
    {
        $course = $this->makeCourse('Laravel');
        $student = laravelstudents::create($this->validStudentData($course->id, [
            'student_code' => 'TEST-001',
            'student_name' => 'Test Student',
            'email' => 'test@example.com',
        ]));

        $this->get('/students/'.$student->id)
            ->assertStatus(200)
            ->assertSee('Test Student')
            ->assertSee('TEST-001')
            ->assertSee('Laravel');

        $this->get('/students/'.$student->id.'/edit')
            ->assertStatus(200)
            ->assertSee('Edit Student')
            ->assertSee('<option value="'.$course->id.'" selected>Laravel</option>', false);
    }

    public function test_student_can_be_updated_without_unique_errors_on_own_values(): void
    {
        $course = $this->makeCourse();
        $other = $this->makeCourse('PHP Basics');
        $student = laravelstudents::create($this->validStudentData($course->id));

        $this->put('/students/'.$student->id, $this->validStudentData($other->id, [
            'student_name' => 'Updated Name',
        ]))->assertRedirect(route('students.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('laravel_students', [
            'id' => $student->id,
            'student_name' => 'Updated Name',
            'course_id' => $other->id,
        ]);
    }

    public function test_student_can_be_deleted(): void
    {
        $course = $this->makeCourse();
        $student = laravelstudents::create($this->validStudentData($course->id));

        $this->delete('/students/'.$student->id)->assertRedirect(route('students.index'));

        $this->assertDatabaseMissing('laravel_students', ['id' => $student->id]);
    }

    public function test_courses_page_lists_courses_and_student_counts(): void
    {
        $course = $this->makeCourse('Laravel Web Development');
        laravelstudents::create($this->validStudentData($course->id));

        $this->get('/courses')
            ->assertStatus(200)
            ->assertSee('Laravel Web Development')
            ->assertSee('LV-101');
    }

    public function test_course_catalog_formats_duration_code_and_table_alignment(): void
    {
        courses::create([
            'course_name' => 'Catalog UI Course',
            'course_code' => 'CS-101',
            'duration_hours' => '120 Hours hrs',
            'course_fee' => '350000',
        ]);

        $this->get('/courses')
            ->assertOk()
            ->assertSee('align-middle', false)
            ->assertSee('course-code-badge', false)
            ->assertSee('CS-101')
            ->assertSee('120 hrs')
            ->assertDontSee('120 Hours hrs')
            ->assertSee('bi-clock', false)
            ->assertSee('bi-tag-fill', false)
            ->assertSee('bi-person-fill', false)
            ->assertSee('btn-cancel-link course-row-action', false)
            ->assertSee('btn-destructive-link course-row-action', false);
    }

    public function test_course_can_be_created_and_name_must_be_unique(): void
    {
        $this->post('/courses', [
            'course_name' => 'Python',
            'course_code' => 'CUSTOM-900',
        ])
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('courses', [
            'course_name' => 'Python',
            'course_code' => 'CS-101',
        ]);
        $this->assertDatabaseMissing('courses', ['course_code' => 'CUSTOM-900']);

        $this->post('/courses', ['course_name' => 'Python'])
            ->assertSessionHasErrors('course_name');
    }

    public function test_course_codes_increment_automatically_and_cannot_be_changed_on_update(): void
    {
        $first = $this->makeCourse('Existing course');
        $first->update(['course_code' => 'CS-105']);

        $this->post('/courses', [
            'course_name' => 'New course',
            'course_code' => 'CUSTOM-900',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'course_name' => 'New course',
            'course_code' => 'CS-106',
        ]);

        $this->post('/courses', [
            'course_name' => 'Another new course',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'course_name' => 'Another new course',
            'course_code' => 'CS-107',
        ]);

        $this->put('/courses/'.$first->id, [
            'course_name' => 'Updated existing course',
            'course_code' => 'CUSTOM-901',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $first->id,
            'course_code' => 'CS-105',
        ]);
    }

    public function test_course_can_be_updated_and_deleted_when_no_students_are_enrolled(): void
    {
        $course = $this->makeCourse('Old Course');

        $this->get('/courses/'.$course->id.'/edit')->assertStatus(200)->assertSee('Edit Course');
        $this->put('/courses/'.$course->id, [
            'course_name' => 'Updated Course',
            'course_code' => 'UP-101',
            'duration_hours' => '50',
            'course_fee' => '250000',
            'description' => 'Updated description',
        ])->assertRedirect(route('courses.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'course_name' => 'Updated Course']);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'course_code' => 'LV-101']);
        $this->delete('/courses/'.$course->id)->assertRedirect(route('courses.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_cannot_be_deleted_while_students_are_enrolled(): void
    {
        $course = $this->makeCourse('Protected Course');
        laravelstudents::create($this->validStudentData($course->id));

        $this->delete('/courses/'.$course->id)
            ->assertRedirect(route('courses.index'))
            ->assertSessionHasErrors('course');
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_about_page_loads_successfully(): void
    {
        $this->get('/about')->assertStatus(200)->assertSee('About StudentHub');
    }

    public function test_contact_page_loads_successfully(): void
    {
        $this->get('/contact')->assertStatus(200)->assertSee('Contact Support');
    }
}

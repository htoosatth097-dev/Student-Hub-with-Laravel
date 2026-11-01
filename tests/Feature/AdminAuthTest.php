<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\courses;
use App\Models\laravelstudents;
use Database\Seeders\AcademicDataSeeder;
use Database\Seeders\DemoRoleUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.recaptcha.site_key' => 'test-site-key',
            'services.recaptcha.secret_key' => 'test-secret-key',
        ]);
    }

    private function fakeSuccessfulRecaptcha(): void
    {
        Http::fake([
            'https://www.recaptcha.net/recaptcha/api/siteverify*' => Http::response(['success' => true], 200),
        ]);
    }

    private function createAdmin(array $override = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'name'     => 'System Administrator',
            'email'    => 'admin@example.com',
            'password' => Hash::make('Secret1234!'),
        ], $override));
    }

    private function createStandardUser(array $override = []): User
    {
        return User::factory()->create(array_merge([
            'name'     => 'Regular User',
            'email'    => 'user@example.com',
            'role'     => 'user',
            'password' => Hash::make('Secret1234!'),
        ], $override));
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to StudentHub');
        $response->assertSee('Admin Security Gateway');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="_token"', false);
        $response->assertSee('g-recaptcha', false);
        $response->assertSee('data-sitekey="test-site-key"', false);
        $response->assertSee('https://www.recaptcha.net/recaptcha/api.js', false);
    }

    public function test_login_page_explains_when_recaptcha_is_not_configured(): void
    {
        config([
            'services.recaptcha.site_key' => null,
            'services.recaptcha.secret_key' => null,
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('reCAPTCHA is not configured.');
        $response->assertDontSee('https://www.recaptcha.net/recaptcha/api.js');
    }

    public function test_authenticated_admin_is_redirected_away_from_login_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('/students');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $this->fakeSuccessfulRecaptcha();

        $admin = $this->createAdmin([
            'email'    => 'superadmin@example.com',
            'password' => Hash::make('Password123#'),
        ]);

        $response = $this->post('/login', [
            'email'                => 'superadmin@example.com',
            'password'             => 'Password123#',
            'g-recaptcha-response' => 'valid-captcha-token',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $this->fakeSuccessfulRecaptcha();

        $this->createAdmin([
            'email'    => 'admin@example.com',
            'password' => Hash::make('CorrectPassword1'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'                => 'admin@example.com',
            'password'             => 'WrongPassword',
            'g-recaptcha-response' => 'valid-captcha-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $this->fakeSuccessfulRecaptcha();

        $response = $this->from('/login')->post('/login', [
            'email'                => 'nonexistent@example.com',
            'password'             => 'AnyPassword123',
            'g-recaptcha-response' => 'valid-captcha-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email', 'password', 'g-recaptcha-response']);
    }

    public function test_login_fails_when_recaptcha_is_missing(): void
    {
        $this->createAdmin([
            'email'    => 'admin@example.com',
            'password' => Hash::make('Password123#'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'admin@example.com',
            'password' => 'Password123#',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_login_fails_with_clear_error_when_recaptcha_is_not_configured(): void
    {
        config([
            'services.recaptcha.site_key' => null,
            'services.recaptcha.secret_key' => null,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'                => 'admin@example.com',
            'password'             => 'Password123#',
            'g-recaptcha-response' => 'some-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'g-recaptcha-response' => 'reCAPTCHA is not configured. Set RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in your environment.',
        ]);
    }

    public function test_login_fails_when_recaptcha_is_invalid(): void
    {
        Http::fake([
            'https://www.recaptcha.net/recaptcha/api/siteverify*' => Http::response([
                'success'     => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $this->createAdmin([
            'email'    => 'admin@example.com',
            'password' => Hash::make('Password123#'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'                => 'admin@example.com',
            'password'             => 'Password123#',
            'g-recaptcha-response' => 'invalid-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_login_succeeds_when_recaptcha_is_valid(): void
    {
        Http::fake([
            'https://www.recaptcha.net/recaptcha/api/siteverify*' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $admin = $this->createAdmin([
            'email'    => 'admin@example.com',
            'password' => Hash::make('Password123#'),
        ]);

        $response = $this->post('/login', [
            'email'                => 'admin@example.com',
            'password'             => 'Password123#',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_closed_when_recaptcha_service_errors(): void
    {
        Http::fake([
            'https://www.recaptcha.net/recaptcha/api/siteverify*' => Http::response(null, 500),
        ]);

        $this->createAdmin([
            'email'    => 'admin@example.com',
            'password' => Hash::make('Password123#'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'                => 'admin@example.com',
            'password'             => 'Password123#',
            'g-recaptcha-response' => 'any-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $this->fakeSuccessfulRecaptcha();
        RateLimiter::clear('spammer@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'                => 'spammer@example.com',
                'password'             => 'wrong',
                'g-recaptcha-response' => 'valid-captcha-token',
            ]);
        }

        // The 6th attempt must trigger rate limiter
        $response = $this->post('/login', [
            'email'                => 'spammer@example.com',
            'password'             => 'wrong',
            'g-recaptcha-response' => 'valid-captcha-token',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }

    public function test_admin_can_logout_successfully(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_logout_rejects_get_requests(): void
    {
        $this->get('/logout')->assertStatus(405);
    }

    public function test_recaptcha_rejects_google_test_secret_in_production(): void
    {
        $this->app['env'] = 'production';
        Http::fake();
        config([
            'services.recaptcha.site_key' => 'test-site-key',
            'services.recaptcha.secret_key' => '6LeIxAcTAAAAAGtest-secret',
        ]);

        $response = $this->withSession(['_token' => 'recaptcha-test-token'])
            ->from('/login')
            ->post('/login', [
                '_token' => 'recaptcha-test-token',
                'email' => 'admin@example.com',
                'password' => 'Password123#',
                'g-recaptcha-response' => 'test-captcha-token',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'g-recaptcha-response' => 'Google reCAPTCHA test secret keys cannot be used in production. Configure a real RECAPTCHA_SECRET_KEY.',
        ]);
        Http::assertNothingSent();
    }

    public function test_unauthenticated_users_cannot_access_students_index(): void
    {
        $response = $this->get('/students');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_cannot_access_student_create_page(): void
    {
        $response = $this->get('/students/create');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_cannot_access_courses(): void
    {
        $response = $this->get('/courses');

        $response->assertRedirect('/login');
    }

    public function test_ordinary_users_cannot_access_students_index_and_receive_403(): void
    {
        $user = $this->createStandardUser();

        $response = $this->actingAs($user)->get('/students');

        $response->assertStatus(403);
    }

    public function test_ordinary_users_cannot_access_student_create_and_receive_403(): void
    {
        $user = $this->createStandardUser();

        $response = $this->actingAs($user)->get('/students/create');

        $response->assertStatus(403);
    }

    public function test_standard_users_can_view_course_catalog_but_not_management_controls(): void
    {
        $user = $this->createStandardUser();

        $this->actingAs($user)->get('/courses')
            ->assertStatus(200)
            ->assertSee('Course Catalog')
            ->assertDontSee('Add New Course')
            ->assertDontSee('Edit')
            ->assertDontSee('Delete');

        $this->actingAs($user)->post('/courses', ['course_name' => 'Not Allowed'])
            ->assertStatus(403);
    }

    public function test_ordinary_users_cannot_delete_students_and_receive_403(): void
    {
        $user = $this->createStandardUser();
        $this->seed(AcademicDataSeeder::class);
        $student = laravelstudents::firstOrFail();

        $response = $this->actingAs($user)->delete('/students/' . $student->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('laravel_students', ['id' => $student->id]);
    }

    public function test_admin_can_access_students_index(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/students');

        $response->assertStatus(200);
        $response->assertSee('Student Directory');
    }

    public function test_admin_can_access_courses(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/courses');

        $response->assertStatus(200);
        $response->assertSee('Courses &amp; Academic Programs', false);
    }

    public function test_student_admin_sees_only_add_student_navigation_and_lands_on_create_form(): void
    {
        $studentAdmin = User::factory()->studentAdmin()->create();

        $this->actingAs($studentAdmin)->get('/students/create')
            ->assertStatus(200)
            ->assertSee('Add Student')
            ->assertDontSee('Student Dashboard')
            ->assertDontSee('About')
            ->assertDontSee('Support')
            ->assertDontSee('<form class="topbar-search"', false);

        $this->assertSame('/students/create', $studentAdmin->landingPath());
    }


    public function test_standard_users_cannot_create_students_or_courses(): void
    {
        $user = $this->createStandardUser();

        $this->actingAs($user)->post('/students', [])->assertStatus(403);
        $this->actingAs($user)->post('/courses', [])->assertStatus(403);
    }

    public function test_student_and_course_policies_deny_standard_users(): void
    {
        $user = $this->createStandardUser();
        $student = new laravelstudents();
        $gate = Gate::forUser($user);

        $this->assertFalse($gate->allows('viewAny', laravelstudents::class));
        $this->assertFalse($gate->allows('view', $student));
        $this->assertFalse($gate->allows('create', laravelstudents::class));
        $this->assertFalse($gate->allows('update', $student));
        $this->assertFalse($gate->allows('delete', $student));
        $this->assertTrue($gate->allows('viewAny', courses::class));
        $this->assertFalse($gate->allows('create', courses::class));
        $course = new courses();
        $this->assertFalse($gate->allows('update', $course));
        $this->assertFalse($gate->allows('delete', $course));
    }

    public function test_student_and_course_policies_allow_administrators(): void
    {
        $admin = $this->createAdmin();
        $student = new laravelstudents();
        $gate = Gate::forUser($admin);

        $this->assertTrue($gate->allows('viewAny', laravelstudents::class));
        $this->assertTrue($gate->allows('view', $student));
        $this->assertTrue($gate->allows('create', laravelstudents::class));
        $this->assertTrue($gate->allows('update', $student));
        $this->assertTrue($gate->allows('delete', $student));
        $this->assertTrue($gate->allows('viewAny', courses::class));
        $this->assertTrue($gate->allows('create', courses::class));
        $this->assertTrue($gate->allows('update', new courses()));
        $this->assertTrue($gate->allows('delete', new courses()));
    }

    public function test_role_cannot_be_set_to_admin_through_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Attempted Admin Escalation',
            'email' => 'mass-assignment@example.com',
            'password' => Hash::make('SecurePass987!'),
            'role' => 'admin',
        ]);

        $this->assertFalse($user->fresh()->isAdmin());
        $this->assertDatabaseHas('users', [
            'email' => 'mass-assignment@example.com',
            'role' => 'user',
        ]);
    }

    public function test_artisan_make_admin_command_creates_admin_account_securely(): void
    {
        $this->artisan('make:admin', [
            '--name'     => 'Artisan Admin',
            '--email'    => 'artisan_admin@studenthub.local',
            '--password' => 'SecurePass987!',
            '--role' => 'super_admin',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name'  => 'Artisan Admin',
            'email' => 'artisan_admin@studenthub.local',
            'role'  => 'super_admin',
        ]);

        $created = User::where('email', 'artisan_admin@studenthub.local')->first();
        $this->assertTrue(Hash::check('SecurePass987!', $created->password));
        $this->assertTrue($created->isAdmin());
    }

    public function test_artisan_admin_create_alias_command_works(): void
    {
        $this->artisan('admin:create', [
            '--name'     => 'Alias Admin',
            '--email'    => 'alias_admin@studenthub.local',
            '--password' => 'AliasPass987!',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name'  => 'Alias Admin',
            'email' => 'alias_admin@studenthub.local',
            'role'  => 'super_admin',
        ]);
    }


    public function test_student_admin_and_course_admin_have_separate_permissions(): void
    {
        $studentAdmin = User::factory()->studentAdmin()->create();
        $courseAdmin = User::factory()->courseAdmin()->create();

        $this->actingAs($studentAdmin)->get('/students')->assertStatus(200);
        $this->actingAs($studentAdmin)->post('/courses', ['course_name' => 'Blocked Course'])->assertStatus(403);

        $this->actingAs($courseAdmin)->get('/courses')->assertStatus(200);
        $this->actingAs($courseAdmin)->get('/students')->assertStatus(403);
        $this->actingAs($courseAdmin)->post('/students', [])->assertStatus(403);
    }

    public function test_super_admin_can_open_both_management_modules(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/students')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/courses')->assertStatus(200);
        $this->assertTrue(Gate::forUser($superAdmin)->allows('manage-users'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('manage-roles'));
    }

    public function test_student_export_is_only_allowed_for_student_admins_and_super_admins(): void
    {
        $studentModel = laravelstudents::class;
        $usersAndPermissions = [
            [User::factory()->studentAdmin()->create(), true],
            [User::factory()->superAdmin()->create(), true],
            [User::factory()->courseAdmin()->create(), false],
            [$this->createStandardUser(), false],
        ];

        foreach ($usersAndPermissions as [$user, $canExport]) {
            $this->assertSame($canExport, Gate::forUser($user)->allows('export', $studentModel));
        }
    }



    public function test_course_admin_can_create_update_and_delete_unenrolled_courses(): void
    {
        $courseAdmin = User::factory()->courseAdmin()->create();
        $this->actingAs($courseAdmin);

        $this->post('/courses', ['course_name' => 'Course Admin Created'])
            ->assertRedirect(route('courses.index'));
        $course = courses::where('course_name', 'Course Admin Created')->firstOrFail();

        $this->put('/courses/'.$course->id, ['course_name' => 'Course Admin Updated'])
            ->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'course_name' => 'Course Admin Updated']);

        $this->delete('/courses/'.$course->id)->assertRedirect(route('courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_student_admin_can_manage_students_but_cannot_edit_or_delete_courses(): void
    {
        $studentAdmin = User::factory()->studentAdmin()->create();
        $course = courses::create(['course_name' => 'Read Only Course']);

        $this->actingAs($studentAdmin)->get('/courses')->assertStatus(200);
        $this->actingAs($studentAdmin)->get('/courses/'.$course->id.'/edit')->assertStatus(403);
        $this->actingAs($studentAdmin)->put('/courses/'.$course->id, ['course_name' => 'Changed'])->assertStatus(403);
        $this->actingAs($studentAdmin)->delete('/courses/'.$course->id)->assertStatus(403);
    }

    public function test_seeded_demo_accounts_can_login_and_are_limited_by_role(): void
    {
        $this->seed([
            AcademicDataSeeder::class,
            DemoRoleUsersSeeder::class,
        ]);
        $this->fakeSuccessfulRecaptcha();

        $expectedAccess = [
            'super_admin' => [200, 200],
            'student_admin' => [200, 200],
            'course_admin' => [403, 200],
            'user' => [403, 200],
        ];

        foreach (DemoRoleUsersSeeder::ACCOUNTS as $role => $email) {
            $this->post('/login', [
                'email' => $email,
                'password' => DemoRoleUsersSeeder::DEMO_PASSWORD,
                'g-recaptcha-response' => 'valid-captcha-token',
            ])->assertRedirect(match ($role) {
                'super_admin' => '/students',
                'student_admin' => '/students/create',
                'course_admin' => '/courses',
                default => '/about',
            });

            [$studentStatus, $courseStatus] = $expectedAccess[$role];
            $this->get('/students')->assertStatus($studentStatus);
            $this->get('/courses')->assertStatus($courseStatus);

            if (in_array($role, ['student_admin', 'user'], true)) {
                $this->post('/courses', ['course_name' => 'Blocked '.$role.' Course'])
                    ->assertStatus(403);
            }

            $this->post('/logout')->assertRedirect(route('login'));
        }
    }

    public function test_signed_in_users_are_redirected_to_their_role_specific_landing_page(): void
    {
        $this->actingAs(User::factory()->studentAdmin()->create())->get('/')->assertRedirect('/students/create');
        $this->actingAs(User::factory()->courseAdmin()->create())->get('/')->assertRedirect('/courses');
        $this->actingAs($this->createStandardUser())->get('/')->assertRedirect('/about');
    }

    public function test_artisan_make_user_creates_a_standard_account_only(): void
    {
        $this->artisan('make:user', [
            '--name' => 'Catalog Reader',
            '--email' => 'reader@example.com',
            '--password' => 'ReaderPass987!',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'reader@example.com',
            'role' => 'user',
        ]);
        $user = User::where('email', 'reader@example.com')->firstOrFail();
        $this->assertFalse($user->isAdmin());
        $this->assertFalse(Gate::forUser($user)->allows('create', courses::class));
    }

    public function test_artisan_make_admin_command_rejects_duplicate_email(): void
    {
        $this->createAdmin(['email' => 'existing@studenthub.local']);

        $this->artisan('make:admin', [
            '--name'     => 'Duplicate Admin',
            '--email'    => 'existing@studenthub.local',
            '--password' => 'ValidPassword123!',
        ])->assertFailed();
    }

    public function test_artisan_make_admin_command_rejects_short_password(): void
    {
        $this->artisan('make:admin', [
            '--name'     => 'Short Pass Admin',
            '--email'    => 'shortpass@studenthub.local',
            '--password' => '123',
        ])->assertFailed();
    }
}

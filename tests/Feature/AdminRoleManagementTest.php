<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperAdmin(): User
    {
        return User::factory()->superAdmin()->create([
            'name'  => 'System Super Admin',
            'email' => 'superadmin@example.com',
        ]);
    }

    private function createStudentAdmin(): User
    {
        return User::factory()->studentAdmin()->create([
            'name'  => 'Academic Student Admin',
            'email' => 'studentadmin@example.com',
        ]);
    }

    private function createCourseAdmin(): User
    {
        return User::factory()->courseAdmin()->create([
            'name'  => 'Curriculum Course Admin',
            'email' => 'courseadmin@example.com',
        ]);
    }

    private function createStandardUser(): User
    {
        return User::factory()->create([
            'name'  => 'Normal User',
            'email' => 'user@example.com',
            'role'  => User::ROLE_USER,
        ]);
    }

    public function test_guests_cannot_access_role_management(): void
    {
        $response = $this->get('/admin/roles');

        $response->assertRedirect('/login');
    }

    public function test_student_admin_is_denied_from_role_management(): void
    {
        $studentAdmin = $this->createStudentAdmin();

        $response = $this->actingAs($studentAdmin)->get('/admin/roles');

        $response->assertStatus(403);
    }

    public function test_course_admin_is_denied_from_role_management(): void
    {
        $courseAdmin = $this->createCourseAdmin();

        $response = $this->actingAs($courseAdmin)->get('/admin/roles');

        $response->assertStatus(403);
    }

    public function test_standard_user_is_denied_from_role_management(): void
    {
        $user = $this->createStandardUser();

        $response = $this->actingAs($user)->get('/admin/roles');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_role_management_page(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/admin/roles');

        $response->assertStatus(200);
        $response->assertSee('Administrator &amp; Role Management', false);
        $response->assertSee('Super Admin Security Gateway', false);
        $response->assertSee('System Super Admin');
    }

    public function test_super_admin_can_update_user_role_to_course_admin(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $targetUser = $this->createStandardUser();

        $this->assertEquals(User::ROLE_USER, $targetUser->role);

        $response = $this->actingAs($superAdmin)->put("/admin/roles/{$targetUser->id}", [
            'role' => User::ROLE_COURSE_ADMIN,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(User::ROLE_COURSE_ADMIN, $targetUser->fresh()->role);
        $this->assertTrue($targetUser->fresh()->isCourseAdmin());
    }

    public function test_super_admin_can_update_user_role_to_student_admin(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $targetUser = $this->createCourseAdmin();

        $this->assertEquals(User::ROLE_COURSE_ADMIN, $targetUser->role);

        $response = $this->actingAs($superAdmin)->put("/admin/roles/{$targetUser->id}", [
            'role' => User::ROLE_STUDENT_ADMIN,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(User::ROLE_STUDENT_ADMIN, $targetUser->fresh()->role);
        $this->assertTrue($targetUser->fresh()->isStudentAdmin());
    }

    public function test_super_admin_cannot_demote_themselves(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->put("/admin/roles/{$superAdmin->id}", [
            'role' => User::ROLE_STUDENT_ADMIN,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertEquals(User::ROLE_SUPER_ADMIN, $superAdmin->fresh()->role);
    }

    public function test_role_update_validates_allowed_roles(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $targetUser = $this->createStandardUser();

        $response = $this->actingAs($superAdmin)->put("/admin/roles/{$targetUser->id}", [
            'role' => 'invalid_role_name',
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_super_admin_can_create_new_sub_admin_account(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->post('/admin/roles', [
            'name'     => 'New Course Manager',
            'email'    => 'newcourses@studenthub.local',
            'role'     => User::ROLE_COURSE_ADMIN,
            'password' => 'SecurePass123$',
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $created = User::where('email', 'newcourses@studenthub.local')->first();
        $this->assertNotNull($created);
        $this->assertEquals('New Course Manager', $created->name);
        $this->assertEquals(User::ROLE_COURSE_ADMIN, $created->role);
        $this->assertTrue(Hash::check('SecurePass123$', $created->password));
    }
}


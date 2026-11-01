<?php

namespace Tests\Feature;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'email'    => 'admin@studenthub.local',
            'name'     => 'System Admin',
            'password' => Hash::make('CurrentPassword123$'),
        ]);
    }

    public function test_forgot_password_page_renders_successfully(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Reset Your Password');
        $response->assertSee('Account Email Address');
        $response->assertSee('Send 6-Digit OTP Code');
    }

    public function test_cannot_request_otp_for_unregistered_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, PasswordResetOtp::count());
    }

    public function test_can_request_otp_for_registered_user(): void
    {
        $user = $this->createUser();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect('/reset-password-otp?email=' . urlencode($user->email));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('password_reset_otps', [
            'email' => $user->email,
        ]);

        $record = PasswordResetOtp::where('email', $user->email)->first();
        $this->assertNotNull($record);
        $this->assertEquals(6, strlen($record->otp));
        $this->assertTrue($record->expires_at->isFuture());
    }

    public function test_reset_password_page_renders_with_email_query(): void
    {
        $response = $this->get('/reset-password-otp?email=admin@studenthub.local');

        $response->assertStatus(200);
        $response->assertSee('Enter Verification Code');
        $response->assertSee('admin@studenthub.local');
        $response->assertSee('6-Digit Verification Code (OTP)');
    }

    public function test_cannot_reset_password_with_invalid_otp(): void
    {
        $user = $this->createUser();
        PasswordResetOtp::create([
            'email'      => $user->email,
            'otp'        => '123456',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->post('/reset-password-otp', [
            'email'                 => $user->email,
            'otp'                   => '999999',
            'password'              => 'NewPassword2026$',
            'password_confirmation' => 'NewPassword2026$',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertTrue(Hash::check('CurrentPassword123$', $user->fresh()->password));
    }

    public function test_cannot_reset_password_with_expired_otp(): void
    {
        $user = $this->createUser();
        PasswordResetOtp::create([
            'email'      => $user->email,
            'otp'        => '654321',
            'expires_at' => now()->subMinutes(5),
        ]);

        $response = $this->post('/reset-password-otp', [
            'email'                 => $user->email,
            'otp'                   => '654321',
            'password'              => 'NewPassword2026$',
            'password_confirmation' => 'NewPassword2026$',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertTrue(Hash::check('CurrentPassword123$', $user->fresh()->password));
    }

    public function test_cannot_reset_password_with_mismatched_confirmation(): void
    {
        $user = $this->createUser();
        PasswordResetOtp::create([
            'email'      => $user->email,
            'otp'        => '654321',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->post('/reset-password-otp', [
            'email'                 => $user->email,
            'otp'                   => '654321',
            'password'              => 'NewPassword2026$',
            'password_confirmation' => 'DifferentPassword2026$',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_successful_password_reset_with_valid_otp(): void
    {
        $user = $this->createUser();
        PasswordResetOtp::create([
            'email'      => $user->email,
            'otp'        => '789012',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->post('/reset-password-otp', [
            'email'                 => $user->email,
            'otp'                   => '789012',
            'password'              => 'BrandNewPassword2026$',
            'password_confirmation' => 'BrandNewPassword2026$',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Verify password updated in DB
        $this->assertTrue(Hash::check('BrandNewPassword2026$', $user->fresh()->password));

        // Verify OTP was consumed/deleted
        $this->assertDatabaseMissing('password_reset_otps', [
            'email' => $user->email,
        ]);
    }
}


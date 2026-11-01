<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    /**
     * OTP expiration duration in minutes.
     */
    private const OTP_EXPIRY_MINUTES = 15;

    /**
     * Maximum allowed OTP requests / verification attempts per minute.
     */
    private const MAX_OTP_ATTEMPTS = 5;

    /**
     * Show the request OTP form (Enter email).
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to the user's email address.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $throttleKey = 'otp-send:' . Str::transliterate($email . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_OTP_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput()->withErrors([
                'email' => "Too many OTP requests. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput()->withErrors([
                'email' => 'We could not find an account registered with that email address.',
            ]);
        }

        RateLimiter::hit($throttleKey, 60);

        // Generate a cryptographically secure 6-digit OTP
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        // Delete any existing active OTP for this email
        PasswordResetOtp::where('email', $email)->delete();

        // Save new OTP
        PasswordResetOtp::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Send Email
        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail(
                otp: $otp,
                userName: $user->name,
                expiresInMinutes: self::OTP_EXPIRY_MINUTES
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset OTP email: ' . $e->getMessage());
        }

        Log::info("Password reset OTP generated for {$email} (expires {$expiresAt})");

        $redirect = redirect()->route('password.otp.reset', ['email' => $email])
            ->with('success', 'A 6-digit verification code (OTP) has been sent to ' . $email . '.');

        if (! app()->isProduction()) {
            $redirect->with('demo_otp', $otp);
        }

        return $redirect;
    }

    /**
     * Show the OTP verification and new password form.
     */
    public function showResetForm(Request $request): View
    {
        $email = (string) $request->query('email', old('email', ''));

        return view('auth.reset-password-otp', compact('email'));
    }

    /**
     * Verify the OTP and reset the user's password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email', 'exists:users,email'],
            'otp'      => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp.size'           => 'The verification code must be exactly 6 digits.',
            'password.confirmed' => 'The new password and confirmation password do not match.',
            'password.min'       => 'The new password must be at least 8 characters long.',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $otp = trim((string) $request->input('otp'));
        $verifyThrottleKey = 'otp-verify:' . Str::transliterate($email . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($verifyThrottleKey, self::MAX_OTP_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($verifyThrottleKey);
            return back()->withInput($request->only('email'))->withErrors([
                'otp' => "Too many verification attempts. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        $record = PasswordResetOtp::where('email', $email)
            ->where('otp', $otp)
            ->first();

        if (! $record) {
            RateLimiter::hit($verifyThrottleKey, 60);
            return back()->withInput($request->only('email', 'otp'))->withErrors([
                'otp' => 'Invalid verification code. Please check the 6-digit OTP code or request a new one.',
            ]);
        }

        if ($record->isExpired()) {
            $record->delete();

            return back()->withInput($request->only('email'))->withErrors([
                'otp' => 'This verification code has expired. Please request a new code.',
            ]);
        }

        RateLimiter::clear($verifyThrottleKey);

        // Update password
        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill([
            'password' => Hash::make((string) $request->input('password')),
        ])->save();

        // Invalidate used OTP
        PasswordResetOtp::where('email', $email)->delete();

        Log::info("Password successfully reset via OTP for user {$email}");

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully! You can now sign in with your new password.');
    }
}

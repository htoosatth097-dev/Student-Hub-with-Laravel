<?php

namespace App\Http\Controllers;

use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Maximum allowed login attempts before throttling.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Decay seconds for rate limiting.
     */
    private const DECAY_SECONDS = 60;

    /**
     * Display the admin login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to(Auth::user()->landingPath());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request with rate limiting and session regeneration.
     */
    public function login(Request $request): RedirectResponse
    {
        // If a session was already active, log it out before validating and logging in the new user
        if (Auth::check()) {
            Auth::logout();
        }

        $rules = [
            'email'                => ['required', 'string', 'email'],
            'password'             => ['required', 'string'],
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ];

        $messages = [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ];

        $request->validate($rules, $messages);

        $credentials = [
            'email'    => strtolower(trim((string) $request->input('email'))),
            'password' => (string) $request->input('password'),
        ];

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => (int) ceil($seconds / 60),
                ]),
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->to($user->landingPath())
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Log the user out of the application and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been signed out successfully.');
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}


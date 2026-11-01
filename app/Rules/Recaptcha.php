<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');

        if (blank(config('services.recaptcha.site_key')) || blank($secretKey)) {
            $fail('reCAPTCHA is not configured. Set RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in your environment.');
            return;
        }

        if (app()->isProduction() && is_string($secretKey) && str_starts_with($secretKey, '6LeIxAcTAAAAAG')) {
            $fail('Google reCAPTCHA test secret keys cannot be used in production. Configure a real RECAPTCHA_SECRET_KEY.');
            return;
        }

        if (empty($value) || ! is_string($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        try {
            $http = Http::asForm()->timeout(5);

            // In local/testing environment or Windows local development without CA certificates,
            // disable cURL SSL verification to avoid "cURL error 60: SSL certificate problem".
            if (! app()->isProduction()) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post('https://www.recaptcha.net/recaptcha/api/siteverify', [
                'secret'   => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (! $response->successful() || ! $response->json('success')) {
                $fail('The reCAPTCHA verification failed. Please try again.');
            }
        } catch (Throwable $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            $fail('Unable to verify reCAPTCHA at this time. Please try again.');
        }
    }
}

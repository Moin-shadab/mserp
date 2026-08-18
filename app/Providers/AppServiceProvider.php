<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \App\Helpers\DatabaseBootstrapper::bootstrap();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Login Brute-Force & Password Cracking Protection (5 attempts per minute per email + IP)
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return Limit::perMinute(5)->by($email . '|' . $request->ip())->response(function () {
                return response()->json([
                    'error' => 'Too many login attempts. Please wait 60 seconds before trying again.'
                ], 429);
            });
        });

        // Dynamic API Rate Limiter (Bypasses limit for System Keys / CLI / Backup scripts)
        RateLimiter::for('api', function (Request $request) {
            $systemKey = $request->header('X-System-Key') ?: $request->input('system_token');
            $validSecret = env('SYSTEM_API_KEY', 'mserp_system_key_secret');

            // 1. System Key, CLI, or Backup Script Exemption -> Unlimited
            if (app()->runningInConsole() || ($systemKey && ($systemKey === config('app.key') || $systemKey === $validSecret))) {
                return Limit::none();
            }

            // 2. Authenticated ERP User -> High Burst Limit (1,000 requests / min)
            if ($user = $request->user()) {
                return Limit::perMinute(1000)->by($user->id);
            }

            // 3. Unauthenticated Guest -> Standard Limit (120 requests / min)
            return Limit::perMinute(120)->by($request->ip())->response(function () {
                return response()->json([
                    'error' => 'API rate limit exceeded. Please slow down your requests or authenticate using a System API Key.'
                ], 429);
            });
        });

        // Strict Rate Limiting for Sensitive Code/Page Generation (30 requests per minute)
        RateLimiter::for('strict_api', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())->response(function () {
                return response()->json([
                    'error' => 'Strict rate limit exceeded for sensitive operation.'
                ], 429);
            });
        });
    }
}


<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceHttps();
        }

        $this->bootRateLimiters();
    }

    /**
     * Configure application rate limiters.
     */
    protected function bootRateLimiters(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(5)
                ->by($email . '|' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;

                    return back()
                        ->withInput($request->except('password'))
                        ->withErrors([
                            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
                        ]);
                });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;

                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors([
                            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
                        ]);
                });
        });
    }
}

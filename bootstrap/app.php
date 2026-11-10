<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureOnboarded;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'account.active' => EnsureAccountIsActive::class,
            'password.changed' => EnsurePasswordChanged::class,
            'onboarded' => EnsureOnboarded::class,
            'role' => EnsureRole::class,
        ]);

        $middleware->redirectUsersTo(function () {
            $route = auth()->user()?->dashboardRoute();

            return $route && \Illuminate\Support\Facades\Route::has($route) ? route($route) : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Check soft delete
            if ($user->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ]);
            }

            // Check account status
            if ($user->status !== AccountStatus::Active) {
                $status = $user->status;

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = match ($status) {
                    AccountStatus::Pending => 'Your account is awaiting administrator approval.',
                    AccountStatus::Rejected => 'Your registration was not approved. Please contact an administrator.',
                    AccountStatus::Suspended => 'Your account has been suspended. Please contact an administrator.',
                    default => 'Your account is not active. Please contact an administrator.',
                };

                return redirect()->route('login')->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
}


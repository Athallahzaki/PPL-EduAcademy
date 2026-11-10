<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->needsOnboarding()) {
            if (! $request->routeIs('onboarding.show', 'onboarding.store', 'logout')) {
                return redirect()->route('onboarding.show');
            }
        }

        return $next($request);
    }
}


<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'redirect:' . $request->ip();
        $maxAttempts = config('affiliate.rate_limit_per_minute', 100);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            abort(429, 'Too many requests');
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
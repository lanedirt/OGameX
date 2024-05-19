<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param ?string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, string|null $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/overview');
        }

        return $next($request);
    }
}

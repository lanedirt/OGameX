<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param ?string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ?string $guard = null)
    {
        if (Auth::check()) {
            if (!Auth::user()->hasRole('admin')) {
                return redirect('/overview');
            }
        }

        return $next($request);
    }
}

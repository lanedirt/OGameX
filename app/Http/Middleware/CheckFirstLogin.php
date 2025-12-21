<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated and this is their first login
        if ($user && $user->first_login && !$user->character_class_free_used) {
            // Don't redirect if already on character class page or if it's an AJAX request
            if (!$request->is('characterclass*') && !$request->ajax() && !$request->wantsJson()) {
                // Mark first login as complete
                $user->first_login = false;
                $user->save();

                // Redirect to character class selection
                return redirect()->route('characterclass.index');
            }
        }

        return $next($request);
    }
}

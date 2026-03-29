<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * If a banned user has an active session, log them out and redirect to the
     * login page with a flash message explaining the ban. Admins are never blocked.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admins cannot be banned via the admin panel, so checking isBanned() alone is
        // sufficient. Avoiding hasRole() here prevents polluting Spatie's eager-loaded
        // roles relation on the shared auth guard user instance between test requests.
        if ($user && $user->isBanned()) {
            $ban   = $user->currentBan();
            $until = $ban?->banned_until
                ? $ban->banned_until->format('Y-m-d H:i') . ' UTC'
                : 'permanently';

            $message = "Your account has been banned: {$ban?->reason}. Expires: {$until}.";

            Auth::logout();
            $request->session()->flash('ban_message', $message);

            return redirect()->route('login');
        }

        return $next($request);
    }
}

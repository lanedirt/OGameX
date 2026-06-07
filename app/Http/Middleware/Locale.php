<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Locale
{
    /**
     * Supported application locales — 27 official OGame community languages.
     *
     * Order matches the dropdown shown in the in-game options page. Codes are
     * the OGame community-server short codes (note: 'ar' = Argentina (Spanish),
     * 'br' = Brazil, 'mx' = Mexico, 'us' = USA, 'yu' = ex-Yugoslavia/Serbian
     * — these are server communities, not strict ISO language codes).
     */
    public const SUPPORTED_LOCALES = [
        'en', 'de', 'it', 'nl',
        'ar', 'br', 'cz', 'dk', 'es', 'fi', 'fr', 'gr',
        'hr', 'hu', 'jp', 'mx', 'pl', 'pt', 'ro', 'ru',
        'se', 'si', 'sk', 'tr', 'tw', 'us', 'yu',
    ];

    /**
     * Handle an incoming request.
     *
     * Resolves the locale in priority order — READ ONLY, never writes session or DB:
     * 1. Session key 'locale'  (written exclusively by LanguageController::switchLang)
     * 2. Authenticated user's  users.lang  DB column
     * 3. Application default   (config/app.php → 'en')
     *
     * NOTE: Do NOT write to the session here. Fortify regenerates the session during
     * login, so any Session::put() performed in middleware before the regeneration is
     * silently lost, creating an infinite re-read loop and potential redirect instability.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // RULE: this middleware is strictly READ-ONLY.
        // It never calls Session::put(), never writes to DB, and never blocks
        // $next($request). Safe to run on every route including login/register,
        // because all operations below are plain reads with no side-effects.

        $locale = null;

        // Priority 1 – explicit user choice stored in session by LanguageController.
        // Uses $request->session() instead of the Session facade so that
        // $request->hasSession() can guard the call safely on stateless contexts.
        if ($request->hasSession() && $request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        // Priority 2 – authenticated user's saved preference in DB.
        // Auth::check() is a read-only call here (no Session::put follows it),
        // so it is safe on POST /login: Fortify's session regeneration happens
        // inside $next($request), after this middleware has already finished.
        if ($locale === null && Auth::check()) {
            $dbLang = Auth::user()->lang ?? null;
            if ($dbLang !== null && in_array($dbLang, self::SUPPORTED_LOCALES, true)) {
                $locale = $dbLang;
            }
        }

        // Apply locale. Falls back to config/app.php 'locale' when $locale is null.
        if ($locale !== null) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}

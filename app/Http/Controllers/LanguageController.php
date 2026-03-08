<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use OGame\Http\Middleware\Locale;

class LanguageController extends OGameController
{
    /**
     * Override IngameTrait's __construct() so that no 'auth' middleware is
     * applied to this controller. /lang/{lang} must be reachable by guests.
     */
    public function __construct()
    {
        // Intentionally empty — do NOT call parent::__construct().
    }

    /**
     * Switch the application language.
     *
     * Validates the requested locale against the supported list (fallback to 'en'
     * if unsupported), persists it in the session, and saves it to the user's
     * database record when the user is authenticated.
     *
     * @param string $lang
     * @return RedirectResponse
     */
    public function switchLang(string $lang): RedirectResponse
    {
        // Fallback to 'en' for any unsupported locale (e.g. /lang/fr → 'en').
        $locale = in_array($lang, Locale::SUPPORTED_LOCALES, true) ? $lang : 'en';

        App::setLocale($locale);

        // Write to session — this is the ONLY place locale is persisted in session.
        session()->put('locale', $locale);

        // Persist to DB so the preference survives session expiry / new devices.
        if (Auth::check()) {
            Auth::user()->lang = $locale;
            Auth::user()->save();
        }

        // Flush session to the store immediately so the locale key is visible to
        // the Locale middleware on the very next request (before session auto-commit).
        session()->save();

        // back($status, $headers, $fallback) — $fallback is the THIRD argument.
        // Passing it as the first would set it as the HTTP status code.
        $fallback = Auth::check() ? route('overview.index') : route('login');

        return redirect()->back(302, [], $fallback);
    }
}

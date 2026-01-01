<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LanguageController extends OGameController
{
    /**
     * Switch the language.
     *
     * @param string $lang
     * @return RedirectResponse
     */
    public function switchLang(string $lang): RedirectResponse
    {
        App::setLocale($lang);
        session()->put('locale', $lang);
        return back();
    }
}

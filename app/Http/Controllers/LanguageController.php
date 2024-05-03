<?php

namespace OGame\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\RedirectResponse;

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

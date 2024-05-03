<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class SearchController extends OGameController
{
    /**
     * Shows the search popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        return view('ingame.search.overlay');
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class PlanetMoveController extends OGameController
{
    /**
     * Shows the notes popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        // TODO: add correct template for this page.
        return view('ingame.notes.overlay');
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class PlanetMoveController extends Controller
{
    use IngameTrait;

    /**
     * Shows the notes popup page
     *
     * @return View
     */
    public function overlay() : View
    {
        // TODO: add correct template for this page.
        return view('ingame.notes.overlay');
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class ChangeNickController extends OGameController
{
    /**
     * Shows the notes popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        return view('ingame.changenick.overlay');
    }
}

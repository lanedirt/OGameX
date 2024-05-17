<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class BuddiesController extends OGameController
{
    /**
     * Shows the buddies index page
     *
     * @return View
     */
    public function index(): View
    {
        return view('ingame.buddies.index');
    }
}

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
    public function index() : View
    {
        $this->setBodyId('buddies');
        return view('ingame.buddies.index');
    }
}

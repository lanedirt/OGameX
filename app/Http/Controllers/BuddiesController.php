<?php

namespace OGame\Http\Controllers;

use OGame\Http\Traits\IngameTrait;
use Illuminate\View\View;

class BuddiesController extends OGameController
{
    use IngameTrait;

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

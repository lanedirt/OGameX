<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class PremiumController extends OGameController
{
    /**
     * Shows the facilities index page
     *
     * @return View
     */
    public function index(): View
    {
        $this->setBodyId('premium');

        return view('ingame.premium.index');
    }
}

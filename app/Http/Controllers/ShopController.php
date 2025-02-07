<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class ShopController extends OGameController
{
    /**
     * Shows the facilities index page
     *
     * @return View
     */
    public function index(): View
    {
        //Bug Fix #418, Ryan Sandnes, 2021-08-02
        $this->setBodyId('shop');

        return view('ingame.shop.index');
    }
}

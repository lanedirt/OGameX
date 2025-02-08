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
        $this->setBodyId('shop');

        return view('ingame.shop.index');
    }
}

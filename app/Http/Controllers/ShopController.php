<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class ShopController extends OGameController
{
    /**
     * Shows the facilities index page
     *
     * @return View
     */
    public function index() : View
    {
        return view('ingame.shop.index');
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class RewardsController extends OGameController
{
    /**
     * Shows the rewards index page
     *
     * @return View
     */
    public function index() : View
    {
        return view('ingame.rewards.index');
    }
}

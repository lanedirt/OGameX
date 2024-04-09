<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class AllianceController extends Controller
{
    use IngameTrait;

    /**
     * Shows the alliance index page
     *
     * @return View
     */
    public function index() : View
    {
        return view('ingame.alliance.index');
    }

    /**
     * Shows the alliance creation page
     *
     * @return View
     */
    public function create() : View
    {
        // TODO: create template.
        return view('ingame.alliance.create');
    }
}

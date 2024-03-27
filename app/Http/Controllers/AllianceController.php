<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;


class AllianceController extends Controller
{
    use IngameTrait;

    /**
     * Shows the alliance index page
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('ingame.alliance.index');
    }

    /**
     * Shows the alliance creation page
     *
     * @return Response
     */
    public function create(Request $request)
    {
        // TODO: create template.
        return view('ingame.alliance.create');
    }
}

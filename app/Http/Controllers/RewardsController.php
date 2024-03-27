<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class RewardsController extends Controller
{
    use IngameTrait;

    /**
     * Shows the rewards index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request)
    {
        return view('ingame.rewards.index');
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class SearchController extends Controller
{
    use IngameTrait;

    /**
     * Shows the search popup page
     *
     * @param int $id
     * @return Response
     */
    public function overlay(Request $request)
    {
        return view('ingame.search.overlay');
    }
}

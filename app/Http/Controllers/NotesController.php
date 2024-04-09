<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class NotesController extends OGameController
{
    use IngameTrait;

    /**
     * Shows the notes popup page
     *
     * @param Request $request
     * @return View
     */
    public function overlay(Request $request) : View
    {
        return view('ingame.notes.overlay');
    }
}

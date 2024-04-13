<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotesController extends OGameController
{
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

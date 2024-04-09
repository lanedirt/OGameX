<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;

class ChangeNickController extends Controller
{
    use IngameTrait;

    /**
     * Shows the notes popup page
     *
     * @return View
     */
    public function overlay() : View
    {
        return view('ingame.changenick.overlay');
    }
}

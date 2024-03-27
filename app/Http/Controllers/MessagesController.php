<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class MessagesController extends Controller
{
    use IngameTrait;

    /**
     * Shows the buddies index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request)
    {
        $this->body_id = 'messages';
        // TODO: create generic return view method which can be used for all pages to return body_id?
        return view('ingame.messages.index')->with([
            'body_id' => 'messages', // Sets <body> tag ID property.
        ]);
    }
}

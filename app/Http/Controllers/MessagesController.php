<?php

namespace OGame\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\MessageService;

class MessagesController extends Controller
{
    use IngameTrait;

    /**
     * Shows the messages index page
     *
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(Request $request, MessageService $messageService)
    {
        // By default open the "Fleets" --> "Espionage" tab.
        $tab = $request->get('tab', 'fleets');
        $subtab = $request->get('subtab', 'espionage');

        // Load messages via MessageService
        $messages = $messageService->getMessagesForTab($tab, $subtab);


        $this->body_id = 'messages';
        // TODO: create generic return view method which can be used for all pages to return body_id?
        return view('ingame.messages.index')->with([
            'body_id' => 'messages', // Sets <body> tag ID property.
        ]);
    }
}

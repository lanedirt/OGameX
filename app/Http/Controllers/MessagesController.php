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
     * Shows the messages index page.
     *
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(Request $request, MessageService $messageService): \Illuminate\Foundation\Application|View|Factory|Application
    {
        // By default open the "Fleets" --> "Espionage" tab.
        $tab = $request->get('tab', 'fleets');
        $subtab = $request->get('subtab', 'espionage');

        $tab = $this->tabContent($messageService, $tab, $subtab);

        // Get unread message count for each tab.
        // TODO: optimize this to get all unread messages count for all tabs in one query.
        $unread_messages_count = [
            'fleets' => $messageService->getUnreadMessagesCountForTab('fleets'),
            'communication' => $messageService->getUnreadMessagesCountForTab('communication'),
            'economy' => $messageService->getUnreadMessagesCountForTab('economy'),
            'universe' => $messageService->getUnreadMessagesCountForTab('universe'),
            'system' => $messageService->getUnreadMessagesCountForTab('system'),
            'favorites' => $messageService->getUnreadMessagesCountForTab('favorites'),
        ];

        $this->body_id = 'messages';
        return view('ingame.messages.index')->with([
            'body_id' => 'messages', // Sets <body> tag ID property.
            'unread_messages_count' => $unread_messages_count,
            'tab_content' => $tab,
        ]);
    }

    protected function tabContent(MessageService $messageService, $tab, $subtab = ''): \Illuminate\Foundation\Application|View|Factory|Application
    {
        $subtab_content = $this->subTabContent($messageService, $tab, $subtab);


        switch ($tab) {
            case 'fleets':
                // Load unread messages count for each subtab based on tab.
                $unread_messages_count = [
                    'espionage' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'espionage'),
                    'combat_reports' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'combat_reports'),
                    'expeditions' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'expeditions'),
                    'transport' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'transport'),
                    'other' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'other'),
                ];

                return view('ingame.messages.tabs.fleets.tab')->with([
                    'subtab_content' => $subtab_content,
                    'unread_messages_count' => $unread_messages_count,
                ]);
            case 'communication':
                // Load unread messages count for each subtab based on tab.
                $unread_messages_count = [
                    'messages' => $messageService->getUnreadMessagesCountForSubTab('communication', 'messages'),
                    'information' => $messageService->getUnreadMessagesCountForSubTab('communication', 'information'),
                ];

                return view('ingame.messages.tabs.communication.tab')->with([
                    'subtab_content' => $subtab_content,
                    'unread_messages_count' => $unread_messages_count,
                ]);
            case 'economy':
            case 'universe':
            case 'system':
            case 'favorites':
                return view('ingame.messages.tabs.default.tab')->with([
                    'subtab_content' => $subtab_content,
                ]);
            default:
                return view('ingame.messages.tabs.fleets.tab')->with([
                    'subtab_content' => $subtab_content,
                ]);
        }
    }

    protected function subTabContent(MessageService $messageService, $tab, $subtab = '')
    {
        $messages = $messageService->getMessagesForTab($tab, $subtab);

        // Return the correct subtab view based on tab.
        switch ($tab) {
            case 'fleets':
                return view('ingame.messages.tabs.fleets.subtab')->with([
                    'messages' => $messages,
                ]);
            case 'communication':
                return view('ingame.messages.tabs.default.subtab')->with([
                    'messages' => $messages,
                ]);
            case 'economy':
            case 'universe':
            case 'system':
            case 'favorites':
                return view('ingame.messages.tabs.default.subtab')->with([
                    'messages' => $messages,
                ]);
            default:
                return view('ingame.messages.tabs.fleets.subtab')->with([
                    'messages' => $messages,
                ]);
        }
    }

    /**
     * Get messages for specific tab and subtab via AJAX.
     *
     * @param Request $request
     * @param MessageService $messageService
     * @return \Illuminate\Foundation\Application|View|Factory|Application
     */
    public function ajax(Request $request, MessageService $messageService): \Illuminate\Foundation\Application|View|Factory|Application
    {
        $tab = $request->get('tab', 'fleets');
        $subtab = $request->get('subtab', '');

        // If no subtab is provided, we load the tab template.
        if (empty($subtab)) {
            return $this->tabContent($messageService, $tab);
        }

        // Otherwise we load the subtab template.
        return $this->subTabContent($messageService, $tab, $subtab);
    }
}

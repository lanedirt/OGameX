<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\MessageService;

class MessagesController extends OGameController
{
    /**
     * Shows the messages index page.
     *
     * @param Request $request
     * @param MessageService $messageService
     * @return View
     */
    public function index(Request $request, MessageService $messageService): View
    {
        $this->setBodyId('messages');

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

        // By default open the "Fleets/Espionage" tab.
        $tabKey = $request->get('tab', 'fleets');
        $subtabKey = $request->get('subtab', 'espionage');

        $tabContent = $this->tabContent($messageService, $tabKey, $subtabKey);

        return view('ingame.messages.index')->with([
            'unread_messages_count' => $unread_messages_count,
            'tab_content' => $tabContent,
        ]);
    }

    /**
     * Return tab content based on tab.
     *
     * @param MessageService $messageService
     * @param string $tab
     * @param string $subtab
     * @return View
     */
    protected function tabContent(MessageService $messageService, string $tab, string $subtab = ''): View
    {
        $subtab_content = $this->subTabContent($messageService, $tab, $subtab);

        switch ($tab) {
            case 'fleets':
                // TODO: optimize this to get all unread messages count for all tabs in one query.
                $unread_messages_count = [
                    'espionage' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'espionage'),
                    'combat_reports' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'combat_reports'),
                    'expeditions' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'expeditions'),
                    'transport' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'transport'),
                    'other' => $messageService->getUnreadMessagesCountForSubTab('fleets', 'other'),
                ];

                return view('ingame.messages.tabs.fleets.tab')->with([
                    'unread_messages_count' => $unread_messages_count,
                ]);
            case 'communication':
                $unread_messages_count = [
                    'messages' => $messageService->getUnreadMessagesCountForSubTab('communication', 'messages'),
                    'information' => $messageService->getUnreadMessagesCountForSubTab('communication', 'information'),
                ];

                return view('ingame.messages.tabs.communication.tab')->with([
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

    /**
     * Return subtab content based on tab.
     *
     * @param MessageService $messageService
     * @param string $tab
     * @param string $subtab
     * @return View
     */
    protected function subTabContent(MessageService $messageService, string $tab, string $subtab = ''): View
    {
        $messages = $messageService->getMessagesForTab($tab, $subtab);

        // Return the correct subtab view based on tab.
        switch ($tab) {
            case 'fleets':
                return view('ingame.messages.tabs.fleets.subtab')->with([
                    'messages' => $messages,
                ]);
            default:
                return view('ingame.messages.tabs.default.subtab')->with([
                    'messages' => $messages,
                ]);
        }
    }

    /**
     * Get messages for specific tab and subtab via AJAX.
     *
     * @param Request $request
     * @param MessageService $messageService
     * @return View
     */
    public function ajaxGetTabContents(Request $request, MessageService $messageService): View
    {
        $tabKey = $request->get('tab', 'fleets');
        $subtabKey = $request->get('subtab', '');

        // If no subtab is provided, we load the tab template.
        if (empty($subtabKey)) {
            return $this->tabContent($messageService, $tabKey);
        }

        // Otherwise we load the subtab template.
        return $this->subTabContent($messageService, $tabKey, $subtabKey);
    }

    /**
     * Returns an individual message for a full screen view.
     *
     * @param int $messageId
     * @param MessageService $messageService
     * @return View
     */
    public function ajaxGetMessage(int $messageId, MessageService $messageService): View
    {
        // Get full message view model.
        $messageObject = $messageService->getFullMessage($messageId);

        return view('ingame.messages.message')->with([
            'messageId' => $messageId,
            'messageBody' => $messageObject->getBodyFull(),
        ]);
    }

    /**
     * Handle POST requests for messages which are used for deleting messages and other things.
     *
     * @param Request $request
     * @param MessageService $messageService
     * @return JsonResponse
     */
    public function post(Request $request, MessageService $messageService): JsonResponse
    {
        $messageId = $request->get('messageId');

        // If action is 103, we delete the message.
        if ((int)$request->get('action') === 103) {
            $messageService->deleteMessage($messageId);
        }

        // Return JSON response with message ID as key and success as value.
        return response()->json([
            $messageId => true,
        ]);
    }
}

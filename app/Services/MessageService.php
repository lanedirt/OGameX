<?php

namespace OGame\Services;

use Exception;
use OGame\Message;

/**
 * Class MessageService.
 *
 * Message Service.
 *
 * @package OGame\Services
 */
class MessageService
{
    /**
     * Define tabs and subtabs which message types they contain.
     */
    protected array $tabs = [
        'fleets' => [
            'espionage' => [
                1, // Espionage report for foreign planet
                2, // Espionage action detected on own planet
            ],
            'combat_reports' => [
                11, // Combat report
            ],
            'expeditions' => [
                21, // Expedition report
            ],
            'transport' => [
                31, // Transport report
            ],
            'other' => [
                41, // Return of fleet
                42, // Outlaw notification
                43, // Wreckage created on own planet after battle
            ],
            'communication' => [
                'messages' => [
                    51, // Buddy request/confirm/delete
                    52, // Alliance message
                ],
            ],
            'economy' => [
                'economy' => [
                    61, // Production canceled
                    62, // Repair completed
                ],
            ],
            'universe' => [
                'universe' => [
                    71, // Welcome message
                    72, // Starter bonus
                    73, // Promotions/sales
                ],
            ],
            'ogame' => [
                'ogame' => [
                    81, // Officer runs out
                ],
            ],
        ],
    ];

    /**
     * The PlayerService object.
     *
     * @var PlayerService
     */
    protected PlayerService $player;

    /**
     * MessageService constructor.
     */
    public function __construct(PlayerService $player)
    {
        $this->player = $player;
    }

    /**
     * Load all planets of specific user.
     *
     * @param string $tab
     * @param string $subtab
     * @return array
     */
    public function getMessagesForTab($tab, $subtab) : array
    {
        // Get all messages of user where type is in the tab and subtab array. Order by created_at desc.
        $messages = Message::where('user_id', $this->player->getId())
            ->whereIn('type', $this->tabs[$tab][$subtab])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach( $messages as $message) {}

        return $messages;
    }
}

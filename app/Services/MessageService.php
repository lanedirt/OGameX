<?php

namespace OGame\Services;

use OGame\Models\Message;
use OGame\ViewModels\MessageViewModel;

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
     *
     * @var array<string, array<string, array<int|string>>> $tabs
     */
    // TODO: refactor this to a typed array/class so sending messages with types is typesafe.
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
                31, // Own fleet reaching a planet
                32, // Resource delivery by foreign fleet
            ],
            'other' => [
                'return_of_fleet' => 41, // Return of fleet
                'outlaw_notification' => 42, // Outlaw notification
                'wreckage_created' => 43, // Wreckage created on own planet after battle
            ],
        ],
        'communication' => [
            'messages' => [
                51, // Buddy request/confirm/delete
                52, // Alliance message
            ],
            'information' => [
                53, // Information
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
                'welcome_message' => 71, // Welcome message
                'starter_bonus' => 72, // Starter bonus
                'promotions' => 73, // Promotions/sales
            ],
        ],
        'system' => [
            'system' => [
                81, // Officer runs out
            ],
        ],
        'favorites' => [
            'favorites' => [
                99, // TODO: Implement favorites
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
     * @return MessageViewModel[] Array of MessageViewModel objects.
     */
    public function getMessagesForTab(string $tab, string $subtab) : array
    {
        // If subtab is empty, we use the first subtab of the tab.
        if (empty($subtab)) {
            $subtab = array_key_first($this->tabs[$tab]);
        }

        // Get all messages of user where type is in the tab and subtab array. Order by created_at desc.
        $messages = Message::where('user_id', $this->player->getId())
            ->whereIn('type', $this->tabs[$tab][$subtab])
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert messages to view models.
        $return = [];
        foreach( $messages as $message) {
            $return[] = new MessageViewModel($message);
        }

        // When the messages are loaded, mark them as viewed.
        foreach ($messages as $message) {
            $message->viewed = 1;
            $message->save();
        }

        return $return;
    }

    public function getUnreadMessagesCount() : int
    {
        return Message::where('user_id', $this->player->getId())
            ->where('viewed', 0)
            ->count();
    }

    public function getUnreadMessagesCountForTab(string $tab) : int
    {
        // Get all ids of the subtabs in this tab.
        $subtabIds = [];
        foreach ($this->tabs[$tab] as $subtab => $types) {
            foreach ($types as $type) {
                $subtabIds[] = $type;
            }
        }

        return Message::where('user_id', $this->player->getId())
            ->whereIn('type', $subtabIds)
            ->where('viewed', 0)
            ->count();
    }

    public function getUnreadMessagesCountForSubTab(string $tab, string $subtab) : int
    {
        return Message::where('user_id', $this->player->getId())
            ->whereIn('type', $this->tabs[$tab][$subtab])
            ->where('viewed', 0)
            ->count();
    }

    /**
     * Sends a message to a player.
     *
     * @param PlayerService $player
     * @param string $subject
     * @param string $body
     * @param string $type
     * @return void
     */
    public function sendMessageToPlayer(PlayerService $player, string $subject, string $body, string $type) : void
    {
        // Convert type string to type int based on tabs array multiple levels.
        $typeId = 0;
        if (is_string($type)) {
            foreach ($this->tabs as $tab => $subtabs) {
                foreach ($subtabs as $subtab => $types) {
                    foreach ($types as $arrayTypeKey => $arrayTypeId) {
                        if ($type === $arrayTypeKey) {
                            $typeId = $arrayTypeId;
                            break;
                        }
                    }
                }
            }
        }

        $message = new Message();
        $message->user_id = $player->getId();
        $message->type = $typeId;
        $message->subject = $subject;
        $message->body = $body;
        $message->save();
    }

    /**
     * Sends a welcome message to the current player.
     *
     * @return void
     */
    public function sendWelcomeMessage(): void
    {
        $this->sendMessageToPlayer($this->player, 'Welcome to OGameX!', 'Greetings Emperor [player]' . $this->player->getId() . '[/player]!

Congratulations on starting your illustrious career. I will be here to guide you through your first steps.

On the left you can see the menu which allows you to supervise and govern your galactic empire.

You’ve already seen the Overview. Resources and Facilities allow you to construct buildings to help you expand your empire. Start by building a Solar Plant to harvest energy for your mines.

Then expand your Metal Mine and Crystal Mine to produce vital resources. Otherwise, simply take a look around for yourself. You’ll soon feel well at home, I’m sure.

You can find more help, tips and tactics here:

Discord Chat: Discord Server
Forum: OGameX Forum
Support: Game Support

You’ll only find current announcements and changes to the game in the forums.


Now you’re ready for the future. Good luck!

This message will be deleted in 7 days.', 'welcome_message');
    }

    /**
     * Deletes a message for the current player.
     *
     * @param int $messageId
     * @return void
     */
    public function deleteMessage(int $messageId) : void
    {
        Message::where('id', $messageId)
            ->where('user_id', $this->player->getId())
            ->delete();
    }
}

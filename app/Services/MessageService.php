<?php

namespace OGame\Services;

use Exception;
use OGame\Factories\GameMessageFactory;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMessages\BattleReport;
use OGame\GameMessages\EspionageReport;
use OGame\GameMessages\WelcomeMessage;
use OGame\Models\Message;
use RuntimeException;

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
     * Define tab and subtab structure.
     *
     * @var array<string, array<string>> $tabs
     */
    private array $tabs = [
        'fleets' => [
            'espionage',
            'combat_reports',
            'expeditions',
            'transport',
            'other',
        ],
        'communication' => [
            'messages',
            'information',
        ],
        'economy' => [
            'economy',
        ],
        'universe' => [
            'universe',
        ],
        'system' => [
            'system',
        ],
        'favorites' => [
            'favorites',
        ],
    ];

    // TODO: tab array defined below is not used anymore but it's kept for reference purposes when implementing
    // more message types.
    /*protected array $tabs = [
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
                'transport_arrived' => 31, // Own fleet reaching a planet
                'transport_received' => 32, // Resource delivery by foreign fleet
            ],
            'other' => [
                'return_of_fleet' => 41, // Return of fleet
                'outlaw_notification' => 42, // Outlaw notification
                'wreckage_created' => 43, // Wreckage created on own planet after battle
                'fleet_deployment' => 44, // Fleet deployment reached the target planet
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
                'production_canceled' => 61, // Production canceled
                'repair_completed' => 62, // Repair completed
                'colony_established' => 63, // Colony established
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
    ];*/

    /**
     * The PlayerService object.
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * MessageService constructor.
     */
    public function __construct(PlayerService $player)
    {
        $this->player = $player;
    }

    /**
     * Sends a system message to a player by using a template and passing params.
     *
     * @param PlayerService $player
     * @param class-string<GameMessage> $gameMessageClass
     * @param array<string,mixed> $params
     * @return void
     */
    public function sendSystemMessageToPlayer(PlayerService $player, string $gameMessageClass, array $params): void
    {
        // Ensure the provided class is a subclass of GameMessage
        if (!is_subclass_of($gameMessageClass, GameMessage::class)) {
            throw new \InvalidArgumentException('Invalid game message class: ' . $gameMessageClass);
        }

        try {
            /** @var GameMessage $gameMessage */
            $gameMessage = resolve($gameMessageClass);

            $message = new Message();
            $message->user_id = $player->getId();
            $message->key = $gameMessage->getKey();
            $message->params = $params;
            $message->save();
        } catch (Exception) {
            throw new RuntimeException('Could not create GameMessage instance while trying to send message.');
        }
    }

    /**
     * Sends an espionage report message to a player with a specific espionage report.
     *
     * @param PlayerService $player
     * @param int $espionageReportId
     * @return Message The created message.
     */
    public function sendEspionageReportMessageToPlayer(PlayerService $player, int $espionageReportId): Message
    {
        try {
            $gameMessage = resolve(EspionageReport::class);
        } catch (Exception) {
            throw new RuntimeException('Could not create espionage report message.');
        }

        $message = new Message();
        $message->user_id = $player->getId();
        $message->key = $gameMessage->getKey();
        $message->espionage_report_id = $espionageReportId;
        $message->save();

        return $message;
    }

    /**
     * Sends a battle report message to a player with a specific battle report.
     *
     * @param PlayerService $player
     * @param int $battleReportId
     * @return Message The created message.
     */
    public function sendBattleReportMessageToPlayer(PlayerService $player, int $battleReportId): Message
    {
        try {
            $gameMessage = resolve(BattleReport::class);
        } catch (Exception) {
            throw new RuntimeException('Could not create battle report message.');
        }

        $message = new Message();
        $message->user_id = $player->getId();
        $message->key = $gameMessage->getKey();
        $message->battle_report_id = $battleReportId;
        $message->save();

        return $message;
    }

    /**
     * Sends a welcome message to the current player.
     *
     * @return void
     */
    public function sendWelcomeMessage(): void
    {
        $this->sendSystemMessageToPlayer($this->player, WelcomeMessage::class, ['player' => '[player]' . $this->player->getId() . '[/player]']);
    }

    /**
     * Load all messages for a specific tab/subtab.
     *
     * @param string $tab
     * @param string $subtab
     * @return GameMessage[] Array of GameMessage objects.
     */
    public function getMessagesForTab(string $tab, string $subtab): array
    {
        // If subtab is empty, we use the first subtab of the tab.
        if (empty($subtab)) {
            $subtab = $this->tabs[$tab][0];
        }

        // Get all messages of user where type is in the tab and subtab array. Order by created_at desc.
        $messageKeys = GameMessageFactory::GetGameMessageKeysByTab($tab, $subtab);
        $messages = Message::where('user_id', $this->player->getId())
            ->whereIn('key', $messageKeys)
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert messages to GameMessage objects.
        $return = [];
        foreach ($messages as $message) {
            $return[] = GameMessageFactory::createGameMessage($message);
        }

        // When the messages are loaded, mark them as viewed.
        foreach ($messages as $message) {
            $message->viewed = 1;
            $message->save();
        }

        return $return;
    }

    /**
     * Get an individual message for the current player to be shown in a full screen overlay view.
     *
     * @param int $messageId
     * @return GameMessage
     */
    public function getFullMessage(int $messageId): GameMessage
    {
        $message = Message::where('id', $messageId)
            ->where('user_id', $this->player->getId())
            ->first();

        if ($message === null) {
            throw new RuntimeException('Message not found.');
        }

        return GameMessageFactory::createGameMessage($message);
    }

    /**
     * Get total unread message count for current player.
     *
     * @return int
     */
    public function getUnreadMessagesCount(): int
    {
        return Message::where('user_id', $this->player->getId())
            ->where('viewed', 0)
            ->count();
    }

    /**
     * Get total unread message count for a specific tab.
     *
     * @param string $tab
     * @return int
     */
    public function getUnreadMessagesCountForTab(string $tab): int
    {
        // Get all keys for the tab.
        $messageKeys = GameMessageFactory::GetGameMessageKeysByTab($tab);

        return Message::where('user_id', $this->player->getId())
            ->whereIn('key', $messageKeys)
            ->where('viewed', 0)
            ->count();
    }

    /**
     * Get total unread message count for a specific tab/subtab.
     *
     * @param string $tab
     * @param string $subtab
     * @return int
     */
    public function getUnreadMessagesCountForSubTab(string $tab, string $subtab): int
    {
        // Get all keys for the subtab.
        $messageKeys = GameMessageFactory::GetGameMessageKeysByTab($tab, $subtab);

        return Message::where('user_id', $this->player->getId())
            ->whereIn('key', $messageKeys)
            ->where('viewed', 0)
            ->count();
    }

    /**
     * Deletes a message for the current player.
     *
     * @param int $messageId
     * @return void
     */
    public function deleteMessage(int $messageId): void
    {
        Message::where('id', $messageId)
            ->where('user_id', $this->player->getId())
            ->delete();
    }
}

<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMessages\BattleReport;
use OGame\GameMessages\ColonyEstablished;
use OGame\GameMessages\ColonyEstablishFailAstrophysics;
use OGame\GameMessages\EspionageReport;
use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMessages\TransportArrived;
use OGame\GameMessages\TransportReceived;
use OGame\GameMessages\WelcomeMessage;
use OGame\Models\Message;

class GameMessageFactory
{
    /**
     * Array of all game message classes. The key is the message key, the value is the class name.
     * This is used to create a new instance of a game message based on a message model.
     *
     * When adding a new game message class, make sure to add it here.
     *
     * @var array<string ,string>
     */
    private static array $gameMessageClasses = [
        'welcome_message' => WelcomeMessage::class,
        'return_of_fleet_with_resources' => ReturnOfFleetWithResources::class,
        'return_of_fleet' => ReturnOfFleet::class,
        'transport_arrived' => TransportArrived::class,
        'transport_received' => TransportReceived::class,
        'colony_established' => ColonyEstablished::class,
        'colony_establish_fail_astrophysics' => ColonyEstablishFailAstrophysics::class,
        'fleet_deployment' => FleetDeployment::class,
        'fleet_deployment_with_resources' => FleetDeploymentWithResources::class,
        'espionage_report' => EspionageReport::class,
        'battle_report' => BattleReport::class,
    ];

    /**
     * @return array<GameMessage>
     */
    public static function getAllGameMessages(): array
    {
        $gameMessages = [];
        foreach (self::$gameMessageClasses as $id => $class) {
            try {
                // Create a new instance of the game message class and pass a new (empty) Message object to it.
                $gameMessages[$id] = app()->make($class, ['message' => new Message()]);
            } catch (BindingResolutionException $e) {
                throw new \RuntimeException('Game message not found: ' . $class);
            }
        }
        return $gameMessages;
    }

    /**
     * Create a game message instance based on a message model.
     *
     * @param Message $message
     * @return GameMessage
     */
    public static function createGameMessage(Message $message): GameMessage
    {
        try {
            return app()->make(self::$gameMessageClasses[$message->key], ['message' => $message]);
        } catch (BindingResolutionException $e) {
            throw new \RuntimeException('Game message not found: ' . $message->key);
        }
    }

    /**
     * Get all class keys that have a certain tab and optionally subtab. This is for knowing which messages to display
     * in the game messages page in what tab/subtab.
     *
     * @param string $tab
     * @param string|null $subtab
     * @return array<int, string>
     */
    public static function GetGameMessageKeysByTab(string $tab, ?string $subtab = null): array
    {
        $matchingKeys = [];

        foreach (self::$gameMessageClasses as $id => $className) {
            try {
                $gameMessage = app()->make($className);
                if ($gameMessage->getTab() === $tab && ($subtab === null || $gameMessage->getSubtab() === $subtab)) {
                    $matchingKeys[] = $id;
                }
            } catch (BindingResolutionException $e) {
                throw new \RuntimeException('Game message not found: ' . $className);
            }

        }

        return $matchingKeys;
    }
}

<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMessages\ColonyEstablished;
use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMessages\TransportArrived;
use OGame\GameMessages\TransportReceived;
use OGame\GameMessages\WelcomeMessage;

class GameMessageFactory
{
    /**
     * @var array<string ,string>
     */
    private static array $gameMessageClasses = [
        'welcome_message' => WelcomeMessage::class,
        'return_of_fleet_with_resources' => ReturnOfFleetWithResources::class,
        'return_of_fleet' => ReturnOfFleet::class,
        'transport_arrived' => TransportArrived::class,
        'transport_received' => TransportReceived::class,
        'colony_established' => ColonyEstablished::class,
        'fleet_deployment' => FleetDeployment::class,
        'fleet_deployment_with_resources' => FleetDeploymentWithResources::class,
    ];

    /**
     * @return array<GameMessage>
     */
    public static function getAllGameMessages(): array
    {
        $gameMessages = [];
        foreach (self::$gameMessageClasses as $id => $class) {
            try {
                $gameMessages[$id] = app()->make($class);
            } catch (BindingResolutionException $e) {
                throw new \RuntimeException('Game message not found: ' . $class);
            }
        }
        return $gameMessages;
    }

    /**
     * @param string $key
     *
     * @return GameMessage
     */
    public static function createGameMessage(string $key): GameMessage
    {
        try {
            return app()->make(self::$gameMessageClasses[$key]);
        } catch (BindingResolutionException $e) {
            throw new \RuntimeException('Game message not found: ' . $key);
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

<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMessages\Abstracts\GameMessage;
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
    ];

    /**
     * @return array<GameMessage>
     * @throws BindingResolutionException
     */
    public static function getAllGameMessages(): array
    {
        $gameMessages = [];
        foreach (self::$gameMessageClasses as $id => $class) {
            $gameMessages[$id] = app()->make($class);
        }
        return $gameMessages;
    }

    /**
     * @param string $key
     *
     * @return GameMessage
     * @throws BindingResolutionException
     */
    public static function createGameMessage(string $key): GameMessage
    {
        if (!isset(self::$gameMessageClasses[$key])) {
            throw new BindingResolutionException("GameMessage with key $key not found.");
        }

        return app()->make(self::$gameMessageClasses[$key]);
    }

    /**
     * Get all class keys that have a certain tab and optionally subtab. This is for knowing which messages to display
     * in the game messages page in what tab/subtab.
     *
     * @param string $tab
     * @param string|null $subtab
     * @return array<int>
     * @throws BindingResolutionException
     */
    public static function getGameMessagesByTab(string $tab, ?string $subtab = null): array
    {
        $matchingKeys = [];

        foreach (self::$gameMessageClasses as $id => $class) {
            $gameMessage = app()->make($class);
            if ($gameMessage->getTab() === $tab && ($subtab === null || $gameMessage->getSubtab() === $subtab)) {
                $matchingKeys[] = $id;
            }
        }

        return $matchingKeys;
    }
}
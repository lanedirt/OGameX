<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;

class GameMessageFactory
{
    /**
     * Array of all game message classes. The key is the message key, the value is the class name.
     * This is used to create a new instance of a game message based on a message model.
     *
     * When adding a new game message class, make sure to add it here.
     *
     * @var array<string, class-string<GameMessage>>
     */
    private static array $gameMessageClasses = [
        'welcome_message' => \OGame\GameMessages\WelcomeMessage::class,
        'return_of_fleet_with_resources' => \OGame\GameMessages\ReturnOfFleetWithResources::class,
        'return_of_fleet' => \OGame\GameMessages\ReturnOfFleet::class,
        'transport_arrived' => \OGame\GameMessages\TransportArrived::class,
        'transport_received' => \OGame\GameMessages\TransportReceived::class,
        'colony_established' => \OGame\GameMessages\ColonyEstablished::class,
        'colony_establish_fail_astrophysics' => \OGame\GameMessages\ColonyEstablishFailAstrophysics::class,
        'fleet_deployment' => \OGame\GameMessages\FleetDeployment::class,
        'fleet_deployment_with_resources' => \OGame\GameMessages\FleetDeploymentWithResources::class,
        'espionage_report' => \OGame\GameMessages\EspionageReport::class,
        'battle_report' => \OGame\GameMessages\BattleReport::class,
        'debris_field_harvest' => \OGame\GameMessages\DebrisFieldHarvest::class,

        'expedition_battle' => \OGame\GameMessages\ExpeditionBattle::class,
        'expedition_dark_matter_found' => \OGame\GameMessages\ExpeditionDarkMatterFound::class,
        'expedition_failed' => \OGame\GameMessages\ExpeditionFailed::class,
        'expedition_failed_and_delay' => \OGame\GameMessages\ExpeditionFailedAndDelay::class,
        'expedition_failed_and_fleet_destroyed' => \OGame\GameMessages\ExpeditionFailedAndFleetDestroyed::class,
        'expedition_failed_and_speedup' => \OGame\GameMessages\ExpeditionFailedAndSpeedup::class,
        'expedition_items_found' => \OGame\GameMessages\ExpeditionItemsFound::class,
        'expedition_resources_found' => \OGame\GameMessages\ExpeditionResourcesFound::class,
        'expedition_units_found' => \OGame\GameMessages\ExpeditionUnitsFound::class,
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
                $gameMessages[$id] = resolve($class, ['message' => new Message()]);
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
            return resolve(self::$gameMessageClasses[$message->key], ['message' => $message]);
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
    public static function GetGameMessageKeysByTab(string $tab, string|null $subtab = null): array
    {
        $matchingKeys = [];

        foreach (self::$gameMessageClasses as $id => $className) {
            try {
                $gameMessage = resolve($className);
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

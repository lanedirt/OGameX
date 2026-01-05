<?php

namespace OGame\Factories;

use OGame\GameMessages\WelcomeMessage;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\TransportArrived;
use OGame\GameMessages\TransportReceived;
use OGame\GameMessages\ColonyEstablished;
use OGame\GameMessages\ColonyEstablishFailAstrophysics;
use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMessages\AcsDefendArrivalSender;
use OGame\GameMessages\AcsDefendArrivalHost;
use OGame\GameMessages\EspionageReport;
use OGame\GameMessages\DefenderEspionageDetected;
use OGame\GameMessages\BattleReport;
use OGame\GameMessages\FleetLostContact;
use OGame\GameMessages\DebrisFieldHarvest;
use OGame\GameMessages\ExpeditionBattle;
use OGame\GameMessages\ExpeditionBattlePirates;
use OGame\GameMessages\ExpeditionBattleAliens;
use OGame\GameMessages\ExpeditionGainDarkMatter;
use OGame\GameMessages\ExpeditionFailed;
use OGame\GameMessages\ExpeditionFailedAndDelay;
use OGame\GameMessages\ExpeditionLossOfFleet;
use OGame\GameMessages\ExpeditionFailedAndSpeedup;
use OGame\GameMessages\ExpeditionGainItem;
use OGame\GameMessages\ExpeditionGainResources;
use OGame\GameMessages\ExpeditionGainShips;
use OGame\GameMessages\ExpeditionMerchantFound;
use OGame\GameMessages\BuddyRequestReceived;
use OGame\GameMessages\BuddyRequestAccepted;
use OGame\GameMessages\BuddyRemoved;
use OGame\GameMessages\MissileAttackReport;
use OGame\GameMessages\MissileDefenseReport;
use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;
use RuntimeException;

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
        'welcome_message' => WelcomeMessage::class,
        'return_of_fleet_with_resources' => ReturnOfFleetWithResources::class,
        'return_of_fleet' => ReturnOfFleet::class,
        'transport_arrived' => TransportArrived::class,
        'transport_received' => TransportReceived::class,
        'colony_established' => ColonyEstablished::class,
        'colony_establish_fail_astrophysics' => ColonyEstablishFailAstrophysics::class,
        'fleet_deployment' => FleetDeployment::class,
        'fleet_deployment_with_resources' => FleetDeploymentWithResources::class,
        'acs_defend_arrival_sender' => AcsDefendArrivalSender::class,
        'acs_defend_arrival_host' => AcsDefendArrivalHost::class,
        'espionage_report' => EspionageReport::class,
        'espionage_detected' => DefenderEspionageDetected::class,
        'battle_report' => BattleReport::class,
        'fleet_lost_contact' => FleetLostContact::class,
        'debris_field_harvest' => DebrisFieldHarvest::class,

        'expedition_battle' => ExpeditionBattle::class,
        'expedition_battle_pirates' => ExpeditionBattlePirates::class,
        'expedition_battle_aliens' => ExpeditionBattleAliens::class,
        'expedition_gain_dark_matter' => ExpeditionGainDarkMatter::class,
        'expedition_failed' => ExpeditionFailed::class,
        'expedition_failed_and_delay' => ExpeditionFailedAndDelay::class,
        'expedition_loss_of_fleet' => ExpeditionLossOfFleet::class,
        'expedition_failed_and_speedup' => ExpeditionFailedAndSpeedup::class,
        'expedition_gain_item' => ExpeditionGainItem::class,
        'expedition_gain_resources' => ExpeditionGainResources::class,
        'expedition_gain_ships' => ExpeditionGainShips::class,
        'expedition_merchant_found' => ExpeditionMerchantFound::class,

        // Buddy system messages
        'buddy_request_received' => BuddyRequestReceived::class,
        'buddy_request_accepted' => BuddyRequestAccepted::class,
        'buddy_removed' => BuddyRemoved::class,

        // Missile attack messages
        'missile_attack_report' => MissileAttackReport::class,
        'missile_defense_report' => MissileDefenseReport::class,
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
                throw new RuntimeException('Game message not found: ' . $class);
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
            throw new RuntimeException('Game message not found: ' . $message->key);
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
                throw new RuntimeException('Game message not found: ' . $className);
            }
        }

        return $matchingKeys;
    }
}

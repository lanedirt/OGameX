<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\GameMessages\Abstracts\ExpeditionGameMessage;
use OGame\Services\PlanetService;
use Exception;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = false;

    /**
     * Returns a list of possible outcomes for an expedition.
     * @return array<array{type: ExpeditionOutcomeType, message: class-string<ExpeditionGameMessage>, resources?: Resources, units?: UnitCollection}>
     */
    protected static function getOutcomes(): array
    {
        return [
            // Resources found:
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound1::class,
                // TODO: some messages have "Entry from the communications officers logbook: It seems that this part of the universe has not been explored yet." appended to it, this one too.
                // TODO2: "Entry from the communications officers logbook: It feels great to be the first ones traveling through an unexplored sector."
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound2::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound3::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound4::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound5::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::ResourcesFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionResourcesFound6::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            // Dark Matter found:
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound1::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound2::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound3::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound4::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound5::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound6::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound7::class,
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound8::class,
                'resources' => new Resources(1, 1, 0, 0),
            ],
            // Units found:
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound1::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound2::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound3::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound4::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound5::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound6::class,
                'units' => new UnitCollection(),
            ],
            [
                'type' => ExpeditionOutcomeType::UnitsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionUnitsFound7::class,
                'units' => new UnitCollection(),
            ],
            // Items found (TODO: add items to the game)
            [
                'type' => ExpeditionOutcomeType::ItemsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionItemsFound1::class,
                //'items' => new ItemCollection(),
            ],
            // Failures:
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed1::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed2::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed3::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed4::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed5::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed6::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed7::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed8::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed9::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed10::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed11::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed12::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed13::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed14::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed15::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Failure,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailed16::class,
            ],
            // Failure (and speed up?)
            [
                'type' => ExpeditionOutcomeType::FailureAndSpeedUp,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndSpeedUp1::class,
            ],
            [
                'type' => ExpeditionOutcomeType::FailureAndSpeedUp,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndSpeedUp2::class,
            ],
            [
                'type' => ExpeditionOutcomeType::FailureAndSpeedUp,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndSpeedUp3::class,
            ],
            // Failure (and delay?)
            [
                'type' => ExpeditionOutcomeType::FailureAndDelay,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndDelay1::class,
            ],
            [
                'type' => ExpeditionOutcomeType::FailureAndDelay,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndDelay2::class,
            ],
            [
                'type' => ExpeditionOutcomeType::FailureAndDelay,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndDelay3::class,
            ],
            // Failure and battle triggered:
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle1::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle2::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle3::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle4::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle5::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle6::class,
            ],
            [
                'type' => ExpeditionOutcomeType::Battle,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionBattle7::class,
            ],
            // Failure and fleet destroyed:
            [
                'type' => ExpeditionOutcomeType::FailureAndFleetDestroyed,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionFailureAndFleetDestroyed1::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function startMissionSanityChecks(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units, Resources $resources): void
    {
        parent::startMissionSanityChecks($planet, $targetCoordinate, $targetType, $units, $resources);

        // Check if there are enough expedition slots available.
        if ($planet->getPlayer()->getExpeditionSlotsInUse() >= $planet->getPlayer()->getExpeditionSlotsMax()) {
            throw new Exception('You are conducting too many expeditions at the same time.');
        }

        // Check if there is at least one non-espionage unit in the fleet.
        if (!$units->hasNonEspionageUnit()) {
            throw new Exception('An expedition must consist of at least one ship.');
        }
    }

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Expedition mission is only possible for position 16.
        if ($targetCoordinate->position !== 16) {
            return new MissionPossibleStatus(false);
        }

        // Only possible if player has astrophysics research level 1 or higher.
        if ($planet->getPlayer()->getResearchLevel('astrophysics') <= 0) {
            return new MissionPossibleStatus(false, __('Fleets cannot be sent to this target. You have to research Astrophysics first.'));
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function processArrival(FleetMission $mission): void
    {
        // TODO: Implement processArrival() method with expedition random events, loot gained etc.
        // TODO: add logic to send confirmation message to player with the results of the expedition.

        // Process the failure outcome.
        // TODO: we should add logic to determine random outcome type, for now we just trigger a failure to test the flow.
        $this->processFailureOutcome($mission);

        // Get a random success outcome.
        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);

        // Create and start the return mission.
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
    }

    /**
     * Process the failure outcome.
     * @param FleetMission $mission
     * @return void
     */
    protected function processFailureOutcome(FleetMission $mission): void
    {
        // Get a random failure outcome.
        // TODO: refactor outcome structure to make it easier to process.
        $outcomes = self::getOutcomes();

        // Get all failure outcomes.
        $failureOutcomes = array_filter($outcomes, fn ($outcome) => $outcome['type'] === ExpeditionOutcomeType::Failure);

        // Get a random failure outcome.
        $failureOutcome = $failureOutcomes[array_rand($failureOutcomes)];

        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the failure outcome.
        // TODO: refactor outcome structure to be statically typed object instead of array.
        // TODO2: each outcome type will probably have its own processing logic too, so might be able to refactor that into the structure as well.
        $this->messageService->sendSystemMessageToPlayer($player, $failureOutcome['message'], []);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Expedition mission: add back the units to the source planet.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}

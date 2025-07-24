<?php

namespace OGame\GameMissions;

use OGame\GameMessages\ExpeditionResourcesFound;
use OGame\GameMessages\ExpeditionUnitsFound;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\ResourceType;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;
use OGame\Services\PlanetService;
use OGame\Services\ObjectService;
use Exception;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = false;

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
        // Get the units of the mission.
        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);

        // TODO: Implement processArrival() method with expedition random events, loot gained etc.
        // TODO: add logic to send confirmation message to player with the results of the expedition.
        $returnResources = new Resources(0, 0, 0, 0);
        $returnUnits = new UnitCollection();

        // Process the failure outcome.
        // TODO: we should add logic to determine random outcome type, for now we just trigger a specific outcome to test the flow.

        // Resources found outcome:
        //$returnResources = $this->processResourcesFoundOutcome($mission);

        // Units found outcome:
        $foundUnits = $this->processUnitsFoundOutcome($mission);
        $units->addCollection($foundUnits);

        // Fleet destroyed outcome:
        //$units = $this->processFleetDestroyedOutcome($mission);

        // Get a random success outcome.
        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
        // TODO: make sure the gained resources are appended to any resources the mission started with?
        // Check the startReturn generic logic for how this should work, as this is not accounted for yet at time of writing.
        $this->startReturn($mission, $returnResources, $units);
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

    /**
    * Returns a list of possible outcomes for an expedition.
    * @return array<array{type: ExpeditionOutcomeType, message: class-string<ExpeditionGameMessage>, resources?: Resources, units?: UnitCollection}>
    */
    private static function getOutcomes(): array
    {
        return [
            // Dark Matter found:
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound1::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound2::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound3::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound4::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound5::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound6::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound7::class,
            ],
            [
                'type' => ExpeditionOutcomeType::DarkMatterFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionDarkMatterFound8::class,
            ],
            // Items found (TODO: add items to the game)
            [
                'type' => ExpeditionOutcomeType::ItemsFound,
                'message' => \OGame\GameMessages\Expeditions\ExpeditionItemsFound1::class,
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
     * Process the failure outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processFailureOutcome(FleetMission $mission): void
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
     * Process the resources found outcome.
     * @param FleetMission $mission
     * @return Resources
     */
    private function processResourcesFoundOutcome(FleetMission $mission): Resources
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // TODO: Determine the resources found at random based on max cargo capacity of the fleet.
        $resourcesFound = new Resources(0, 0, 0, 0);

        // A expedition always returns a single resource unit, so first determine which resource unit is returned.
        $resourceAmount = random_int(1, 100);

        // Pick random number between 0 and 2 to determine the resource type.
        $resource_type_int = random_int(0, 2);
        switch ($resource_type_int) {
            case 0:
                $resource_type = ResourceType::Metal;
                $resourcesFound->metal->set($resourceAmount);
                break;
            case 1:
                $resource_type = ResourceType::Crystal;
                $resourcesFound->crystal->set($resourceAmount);
                break;
            case 2:
                $resource_type = ResourceType::Deuterium;
                $resourcesFound->deuterium->set($resourceAmount);
                break;
        }

        // Send a message to the player with the resources found outcome.
        // Choose a random message variation id based on the number of available outcomes.
        $message_variation_id = ExpeditionResourcesFound::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionResourcesFound::class, ['message_variation_id' => $message_variation_id, 'resource_type' => $resource_type->value, 'resource_amount' => $resourceAmount]);

        return $resourcesFound;
    }

    /**
     * Process the units found outcome.
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function processUnitsFoundOutcome(FleetMission $mission): UnitCollection
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Make random array of units with random amount.
        $possible_units = ['light_fighter', 'heavy_fighter', 'espionage_probe', 'small_cargo', 'large_cargo'];

        // Get 1-3 random unit types
        $num_units = random_int(1, 3);
        shuffle($possible_units);
        $random_unit_types = array_slice($possible_units, 0, $num_units);

        // Create a random amount of units for each unit type.
        $units = new UnitCollection();
        $objectService = app(ObjectService::class);
        foreach ($random_unit_types as $unit_type) {
            $units->addUnit($objectService->getShipObjectByMachineName($unit_type), random_int(1, 100));
        }

        // Convert units to array with key "unit_<unit_id>" and value as amount.
        $message_params = [];
        foreach ($units->units as $unit) {
            $message_params['unit_' . $unit->unitObject->id] = $unit->amount;
        }

        // Send a message to the player with the units found outcome.
        // Choose a random message variation id based on the number of available outcomes.
        $message_variation_id = ExpeditionUnitsFound::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionUnitsFound::class, ['message_variation_id' => $message_variation_id] + $message_params);

        return $units;
    }

    /**
     * Process the fleet destroyed outcome and return the units that are left (which is empty as the fleet is destroyed).
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function processFleetDestroyedOutcome(FleetMission $mission): UnitCollection
    {
        // Get a random failure outcome.
        // TODO: refactor outcome structure to make it easier to process.
        $outcomes = self::getOutcomes();

        // Get all fleet destroyed outcomes.
        $fleetDestroyedOutcomes = array_filter($outcomes, fn ($outcome) => $outcome['type'] === ExpeditionOutcomeType::FailureAndFleetDestroyed);

        // Get a random fleet destroyed outcome.
        $fleetDestroyedOutcome = $fleetDestroyedOutcomes[array_rand($fleetDestroyedOutcomes)];

        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the fleet destroyed outcome.
        $this->messageService->sendSystemMessageToPlayer($player, $fleetDestroyedOutcome['message'], []);

        // Return empty unit collection as the whole fleet is destroyed.
        return new UnitCollection();
    }
}

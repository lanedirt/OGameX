<?php

namespace OGame\GameMissions;

use OGame\GameMessages\ExpeditionFailed;
use OGame\GameMessages\ExpeditionFailedAndDelay;
use OGame\GameMessages\ExpeditionFailedAndSpeedup;
use OGame\GameMessages\ExpeditionGainResources;
use OGame\GameMessages\ExpeditionGainShips;
use OGame\GameMessages\ExpeditionGainDarkMatter;
use OGame\GameMessages\ExpeditionGainItem;
use OGame\GameMessages\ExpeditionLossOfFleet;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\ResourceType;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
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

        $returnResources = new Resources(0, 0, 0, 0);

        // Select a random outcome based on configuration and weights
        $outcome = $this->selectRandomOutcome();

        switch ($outcome) {
            case 'expedition_failed':
                $this->processExpeditionFailedOutcome($mission);
                break;
            case 'expedition_failed_and_delay':
                $this->processExpeditionFailedAndDelayOutcome($mission);
                break;
            case 'expedition_failed_and_speedup':
                $this->processExpeditionFailedAndSpeedupOutcome($mission);
                break;
            case 'expedition_gain_ships':
                $foundUnits = $this->processExpeditionGainShipsOutcome($mission);
                $units->addCollection($foundUnits);
                break;
            case 'expedition_gain_dark_matter':
                $this->processExpeditionGainDarkMatterOutcome($mission);
                break;
            case 'expedition_gain_resources':
                $returnResources = $this->processExpeditionGainResourcesOutcome($mission);
                break;
            case 'expedition_gain_merchant_trade':
                $returnResources = $this->processExpeditionGainMerchantTradeOutcome($mission);
                break;
            case 'expedition_gain_item':
                $this->processExpeditionGainItemOutcome($mission);
                break;
            case 'expedition_loss_of_fleet':
                $units = $this->processExpeditionLossOfFleetOutcome($mission);
                break;
        }

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
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
     * Select a random expedition outcome based on server settings and weights.
     * Fleet destroyed outcomes have 2% chance each, others are equally distributed.
     *
     * @return string
     */
    private function selectRandomOutcome(): string
    {
        $settingsService = app(SettingsService::class);

        // Build array of enabled outcomes
        $enabledOutcomes = [];

        if ($settingsService->expeditionFailedEnabled()) {
            $enabledOutcomes[] = 'expedition_failed';
        }

        if ($settingsService->expeditionFailedAndDelayEnabled()) {
            $enabledOutcomes[] = 'expedition_failed_and_delay';
        }

        if ($settingsService->expeditionFailedAndSpeedupEnabled()) {
            $enabledOutcomes[] = 'expedition_failed_and_speedup';
        }

        if ($settingsService->expeditionGainShipsEnabled()) {
            $enabledOutcomes[] = 'expedition_gain_ships';
        }

        if ($settingsService->expeditionGainResourcesEnabled()) {
            $enabledOutcomes[] = 'expedition_gain_resources';
        }

        if ($settingsService->expeditionLossOfFleetEnabled()) {
            $enabledOutcomes[] = 'expedition_loss_of_fleet';
        }

        // TODO: Implement merchant trade, dark matter and item outcomes.
        /*if ($settingsService->expeditionGainMerchantTradeEnabled()) {
            $enabledOutcomes[] = 'expedition_gain_merchant_trade';
        }

        if ($settingsService->expeditionGainDarkMatterEnabled()) {
            $enabledOutcomes[] = 'expedition_gain_dark_matter';
        }

        if ($settingsService->expeditionGainItemEnabled()) {
            $enabledOutcomes[] = 'expedition_gain_item';
        }*/

        // If no outcomes are enabled, default to failure
        if (empty($enabledOutcomes)) {
            return 'expedition_failed';
        }

        // If only one outcome is enabled, return it
        if (count($enabledOutcomes) === 1) {
            return $enabledOutcomes[0];
        }

        // If loss of fleet is enabled, give it 2% chance, rest split evenly
        if (in_array('expedition_loss_of_fleet', $enabledOutcomes)) {
            if (random_int(1, 100) <= 2) {
                return 'expedition_loss_of_fleet';
            }

            // Remove loss of fleet from outcomes for even distribution
            $enabledOutcomes = array_diff($enabledOutcomes, ['expedition_loss_of_fleet']);
        }

        // Pick random outcome from remaining enabled outcomes
        return $enabledOutcomes[array_rand($enabledOutcomes)];
    }

    /**
     * Process the expedition failed outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionFailedOutcome(FleetMission $mission): void
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the failure outcome.
        $message_variation_id = ExpeditionFailed::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionFailed::class, ['message_variation_id' => $message_variation_id]);
    }

    /**
     * Process the expedition failed and delay outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionFailedAndDelayOutcome(FleetMission $mission): void
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the failure and delay outcome.
        $message_variation_id = ExpeditionFailedAndDelay::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionFailedAndDelay::class, ['message_variation_id' => $message_variation_id]);
    }

    /**
     * Process the expedition failed and speedup outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionFailedAndSpeedupOutcome(FleetMission $mission): void
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the failure and speedup outcome.
        $message_variation_id = ExpeditionFailedAndSpeedup::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionFailedAndSpeedup::class, ['message_variation_id' => $message_variation_id]);
    }

    /**
     * Process the expedition gain resources outcome.
     * @param FleetMission $mission
     * @return Resources
     */
    private function processExpeditionGainResourcesOutcome(FleetMission $mission): Resources
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
        $message_variation_id = ExpeditionGainResources::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainResources::class, ['message_variation_id' => $message_variation_id, 'resource_type' => $resource_type->value, 'resource_amount' => $resourceAmount]);

        return $resourcesFound;
    }

    /**
     * Process the expedition gain ships outcome.
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function processExpeditionGainShipsOutcome(FleetMission $mission): UnitCollection
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
        $message_variation_id = ExpeditionGainShips::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainShips::class, ['message_variation_id' => $message_variation_id] + $message_params);

        return $units;
    }

    /**
     * Process the expedition gain dark matter outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionGainDarkMatterOutcome(FleetMission $mission): void
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // TODO: Implement dark matter giving logic
        // For now, just send the message
        $message_variation_id = ExpeditionGainDarkMatter::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainDarkMatter::class, ['message_variation_id' => $message_variation_id]);

        // TODO: Add actual dark matter to player when dark matter itself is implemented.
    }

    /**
     * Process the expedition gain merchant trade outcome.
     * @param FleetMission $mission
     * @return Resources
     */
    private function processExpeditionGainMerchantTradeOutcome(FleetMission $mission): Resources
    {
        // TODO: Implement merchant trade logic
        // For now, return no resources but this should implement trading logic
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // TODO: Send appropriate message once ExpeditionMerchantTrade message class exists
        // $message_variation_id = ExpeditionMerchantTrade::getRandomMessageVariationId();
        // $this->messageService->sendSystemMessageToPlayer($player, ExpeditionMerchantTrade::class, ['message_variation_id' => $message_variation_id]);

        return new Resources(0, 0, 0, 0);
    }

    /**
     * Process the expedition gain item outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionGainItemOutcome(FleetMission $mission): void
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the item found outcome.
        $message_variation_id = ExpeditionGainItem::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainItem::class, ['message_variation_id' => $message_variation_id]);

        // TODO: Implement actual item giving logic when items themselves are implemented.
    }

    /**
     * Process the expedition loss of fleet outcome.
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function processExpeditionLossOfFleetOutcome(FleetMission $mission): UnitCollection
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Send a message to the player with the fleet destroyed outcome.
        $message_variation_id = ExpeditionLossOfFleet::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionLossOfFleet::class, ['message_variation_id' => $message_variation_id]);

        // Return empty unit collection as the whole fleet is destroyed.
        return new UnitCollection();
    }
}

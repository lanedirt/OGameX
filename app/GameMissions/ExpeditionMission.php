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
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\ShipObject;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\ResourceType;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\Highscore;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Services\PlanetService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Exception;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = true;

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

        // This is the additional time that is added to the return mission's arrival time.
        // This can be either a positive number (delay) or a negative number (speedup).
        $additionalReturnTripTime = 0;

        // If the mission is not processed yet, we need to process the outcome.
        // Select a random outcome based on configuration and weights
        $outcome = $this->selectRandomOutcome();

        switch ($outcome) {
            case ExpeditionOutcomeType::Failed:
                $this->processExpeditionFailedOutcome($mission);
                break;
            case ExpeditionOutcomeType::FailedAndDelay:
                $additionalReturnTripTime = $this->processExpeditionFailedAndDelayOutcome($mission);
                break;
            case ExpeditionOutcomeType::FailedAndSpeedup:
                $additionalReturnTripTime = $this->processExpeditionFailedAndSpeedupOutcome($mission);
                break;
            case ExpeditionOutcomeType::GainShips:
                $foundUnits = $this->processExpeditionGainShipsOutcome($mission);
                $units->addCollection($foundUnits);
                break;
            case ExpeditionOutcomeType::GainDarkMatter:
                $this->processExpeditionGainDarkMatterOutcome($mission);
                break;
            case ExpeditionOutcomeType::GainResources:
                $returnResources = $this->processExpeditionGainResourcesOutcome($mission);
                break;
            case ExpeditionOutcomeType::GainMerchantTrade:
                $this->processExpeditionGainMerchantTradeOutcome($mission);
                break;
            case ExpeditionOutcomeType::GainItems:
                $this->processExpeditionGainItemOutcome($mission);
                break;
            case ExpeditionOutcomeType::LossOfFleet:
                $units = $this->processExpeditionLossOfFleetOutcome($mission);
                break;
        }

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
        $this->startReturn($mission, $returnResources, $units, $additionalReturnTripTime);
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
     * @return int
     */
    private function processExpeditionFailedAndDelayOutcome(FleetMission $mission): int
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Pick a random delay percentage between 5% and 30%.
        $additionalReturnTripTimePercentage = random_int(5, 10);

        // Calculate one way mission duration.
        $onewayMissionDuration = ($mission->time_arrival - $mission->time_departure) + $mission->time_holding;

        // Calculate the additional return trip time in seconds based on the mission's original duration + holding time.
        $additionalReturnTripTime = intval($onewayMissionDuration * ($additionalReturnTripTimePercentage / 100));

        // Send a message to the player with the failure and delay outcome.
        $message_variation_id = ExpeditionFailedAndDelay::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionFailedAndDelay::class, ['message_variation_id' => $message_variation_id]);

        return $additionalReturnTripTime;
    }

    /**
     * Process the expedition failed and speedup outcome.
     * @param FleetMission $mission
     * @return int
     */
    private function processExpeditionFailedAndSpeedupOutcome(FleetMission $mission): int
    {
        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // Pick a random speedup percentage between 5% and 30%.
        $additionalReturnTripTimePercentage = random_int(5, 10);

        // Calculate one way mission duration.
        $onewayMissionDuration = ($mission->time_arrival - $mission->time_departure) + $mission->time_holding;

        // Calculate the subtraction from the return trip time in seconds based on the mission's original duration + holding time.
        // Note: this value is negative on purpose, so it will be subtracted from the default return trip time.
        $additionalReturnTripTime = -intval($onewayMissionDuration * ($additionalReturnTripTimePercentage / 100));

        // Send a message to the player with the failure and speedup outcome.
        $message_variation_id = ExpeditionFailedAndSpeedup::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionFailedAndSpeedup::class, ['message_variation_id' => $message_variation_id]);

        return $additionalReturnTripTime;
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

        // Max resources found is according to these params:
        // Number 1 player highscore "general" points determines the max resources found:
        // < 10.000 points: 40.000 metal
        // < 100.000 points: 500.000 metal
        // < 1.000.000 points: 1.200.000 metal
        // < 5.000.000 points: 1.800.000 metal
        // < 25.000.000 points: 2.400.000 metal
        // < 50.000.000 points: 3.000.000 metal
        // < 75.000.000 points: 3.600.000 metal
        // < 100.000.000 points: 4.200.000 metal
        // > 100.000.000 points: 5.000.000 metal
        // When discoverer class is active, the following modifier applies: economy speed * 1.5.
        // E.g. with discoverer class and economy speed 5x, the modifier is 7.5.
        // so the >100m points max find would be 7.5 * 5m = 37.5m
        // Crystal is 2/3 of the metal find.
        // Deuterium is 1/3 of the metal find.
        $rank_1_highscore_points = Highscore::orderByDesc(HighscoreTypeEnum::general->name)->first()->general;

        if ($rank_1_highscore_points < 10000) {
            $max = 40000;
        } elseif ($rank_1_highscore_points < 100000) {
            $max = 500000;
        } elseif ($rank_1_highscore_points < 1000000) {
            $max = 1200000;
        } elseif ($rank_1_highscore_points < 5000000) {
            $max = 1800000;
        } elseif ($rank_1_highscore_points < 25000000) {
            $max = 2400000;
        } elseif ($rank_1_highscore_points < 50000000) {
            $max = 3000000;
        } elseif ($rank_1_highscore_points < 75000000) {
            $max = 3600000;
        } elseif ($rank_1_highscore_points < 100000000) {
            $max = 4200000;
        } else {
            $max = 5000000;
        }

        // Set min to at least 10% of the max.
        $min = max(1, (int)floor($max * 0.1));

        // Pick a random amount between min and max.
        $resourceAmount = random_int($min, $max);

        // TODO: when actual player classes such as discoverer, collector etc. are implemented, make this modifier apply only if class is "discoverer".
        // For now we apply it anyway so the economy speed is applied to the resource find.
        $economySpeed = $this->settings->economySpeed();
        $resourceAmount = $resourceAmount * ($economySpeed * 1.5);

        // Get max cargo capacity of the fleet.
        // TODO: also implement check how much resources the fleet is already carrying, to not exceed the max cargo capacity that way?
        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);
        $maxCargoCapacity = $units->getTotalCargoCapacity($player);

        // Determine the resource type: metal, crystal or deuterium.
        $cargoCapacityConstrainedAmount = 0;
        $resource_type_int = random_int(0, 2);
        switch ($resource_type_int) {
            case 0:
                $resource_type = ResourceType::Metal;

                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $resourceAmount);
                $resourcesFound->metal->set($cargoCapacityConstrainedAmount);
                break;
            case 1:
                $resource_type = ResourceType::Crystal;

                $adjustedResourceAmount = $resourceAmount * (2 / 3);
                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $adjustedResourceAmount);
                $resourcesFound->crystal->set($cargoCapacityConstrainedAmount);
                break;
            case 2:
                $resource_type = ResourceType::Deuterium;

                $adjustedResourceAmount = $resourceAmount * (1 / 3);
                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $adjustedResourceAmount);
                $resourcesFound->deuterium->set($cargoCapacityConstrainedAmount);
                break;
        }

        // Send a message to the player with the resources found outcome.
        // Choose a random message variation id based on the number of available outcomes.
        $message_variation_id = ExpeditionGainResources::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainResources::class, ['message_variation_id' => $message_variation_id, 'resource_type' => $resource_type->value, 'resource_amount' => $cargoCapacityConstrainedAmount]);

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

        // Get the expedition fleet units to determine what ships can be found
        $fleetUnits = $this->fleetMissionService->getFleetUnits(mission: $mission);
        $objectService = app(ObjectService::class);

        // Define expedition hierarchy levels - each level can find ships at that level and all lower levels.
        // TODO: when implementing pathfinder and reaper units, add them to the levels array.
        // Pathfinder = between cruiser and battle_ship, Reaper = after destroyer.
        // NOTE: some ships are not able to be found on expeditions on purpose: deathstar, colony ship, recycler, solar satellite.
        $expeditionLevels = [
            1 => ['small_cargo', 'light_fighter', 'espionage_probe'],
            2 => ['large_cargo'],
            3 => ['heavy_fighter'],
            4 => ['cruiser'],
            5 => ['battle_ship'],
            6 => ['battlecruiser'],
            7 => ['bomber'],
            8 => ['destroyer'],
        ];

        // Helper function to find which level a ship belongs to
        $getShipLevel = function($shipMachineName) use ($expeditionLevels) {
            foreach ($expeditionLevels as $level => $ships) {
                if (in_array($shipMachineName, $ships)) {
                    return $level;
                }
            }
            return 0;
        };

        // Find the highest expedition level in the fleet
        $maxExpeditionLevel = 0;
        foreach ($fleetUnits->units as $unit) {
            if ($unit->unitObject instanceof ShipObject) {
                $shipMachineName = $unit->unitObject->machine_name;
                $shipLevel = $getShipLevel($shipMachineName);
                if ($shipLevel > 0) {
                    $maxExpeditionLevel = max($maxExpeditionLevel, $shipLevel);
                }
            }
        }

        // If no expedition-capable ships found, default to level 1
        if ($maxExpeditionLevel === 0) {
            $maxExpeditionLevel = 1;
        }

        // Collect all ships that can be found at this level and below
        $possibleShipMachineNames = [];
        for ($level = 1; $level <= $maxExpeditionLevel; $level++) {
            if (isset($expeditionLevels[$level])) {
                $possibleShipMachineNames = array_merge($possibleShipMachineNames, $expeditionLevels[$level]);
            }
        }

        // Get ship objects for the possible ships
        $possibleShips = [];
        foreach ($possibleShipMachineNames as $machineName) {
            try {
                $ship = $objectService->getShipObjectByMachineName($machineName);
                $possibleShips[] = $ship;
            } catch (Exception $e) {
                // Ship not found. Skip it.
                continue;
            }
        }

        // If no ships can be found, return empty collection
        if (empty($possibleShips)) {
            return new UnitCollection();
        }

        // Select 1-3 random ship types from possible ships
        $num_ship_types = min(random_int(1, 3), count($possibleShips));
        shuffle($possibleShips);
        $selectedShips = array_slice($possibleShips, 0, $num_ship_types);

        // Create a random amount of units for each selected ship type
        $units = new UnitCollection();
        foreach ($selectedShips as $ship) {
            // TODO: Implement proper amount calculation based on:
            // - Server top player points
            // - Economy speed
            // - Discoverer class bonus
            // - Max unit value of 500,000-600,000
            // For now, using a simple random amount
            $amount = random_int(1, 100);
            $units->addUnit($ship, $amount);
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

        // TODO: Add actual dark matter to player when dark matter itself is implemented.

        $message_variation_id = ExpeditionGainDarkMatter::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainDarkMatter::class, ['message_variation_id' => $message_variation_id]);
    }

    /**
     * Process the expedition gain merchant trade outcome.
     * @param FleetMission $mission
     * @return void
     */
    private function processExpeditionGainMerchantTradeOutcome(FleetMission $mission): void
    {
        // TODO: Implement merchant trade logic
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        // TODO: Send appropriate message once ExpeditionMerchantTrade message class exists
        // $message_variation_id = ExpeditionMerchantTrade::getRandomMessageVariationId();
        // $this->messageService->sendSystemMessageToPlayer($player, ExpeditionMerchantTrade::class, ['message_variation_id' => $message_variation_id]);
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

        // TODO: Implement actual item giving logic when items themselves are implemented.

        // Send a message to the player with the item found outcome.
        $message_variation_id = ExpeditionGainItem::getRandomMessageVariationId();
        $this->messageService->sendSystemMessageToPlayer($player, ExpeditionGainItem::class, ['message_variation_id' => $message_variation_id]);
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

    /**
     * Select a random expedition outcome based on server settings and weights.
     * Fleet destroyed outcomes have 2% chance each, others are equally distributed.
     *
     * @return ExpeditionOutcomeType
     */
    private function selectRandomOutcome(): ExpeditionOutcomeType
    {
        $settingsService = app(SettingsService::class);

        // Build array of enabled outcomes
        $enabledOutcomes = [];

        // Create array of all outcomes that are enabled in the settings.
        foreach (ExpeditionOutcomeType::cases() as $outcome) {
            if ($settingsService->get($outcome->getSettingKey()) === '1') {
                $enabledOutcomes[] = $outcome;
            }
        }

        // Remove expedition outcomes that are not fully implemented yet to avoid them from being selected.
        // TODO: remove the filter once the outcomes below are fully implemented.
        $enabledOutcomes = array_filter($enabledOutcomes, function ($outcome) {
            return $outcome !== ExpeditionOutcomeType::GainDarkMatter && $outcome !== ExpeditionOutcomeType::GainItems && $outcome !== ExpeditionOutcomeType::GainMerchantTrade;
        });

        // If no outcomes are enabled, default to failure
        if (empty($enabledOutcomes)) {
            return ExpeditionOutcomeType::Failed;
        }

        // If only one outcome is enabled, return it
        if (count($enabledOutcomes) === 1) {
            return $enabledOutcomes[0];
        }

        // If loss of fleet is enabled, give it 2% chance, rest split evenly
        if (in_array(ExpeditionOutcomeType::LossOfFleet, $enabledOutcomes)) {
            if (random_int(1, 100) <= 2) {
                return ExpeditionOutcomeType::LossOfFleet;
            }

            // Remove loss of fleet from outcomes for even distribution
            $enabledOutcomes = array_values(array_filter($enabledOutcomes, function ($outcome) {
                return $outcome !== ExpeditionOutcomeType::LossOfFleet;
            }));
        }

        // Pick random outcome from remaining enabled outcomes
        return $enabledOutcomes[array_rand($enabledOutcomes)];
    }
}

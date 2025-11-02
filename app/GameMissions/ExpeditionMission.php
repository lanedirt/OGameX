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
     * Configurable outcome weights based on community research.
     * Each outcome has a weight out of 1000 (representing 0.1% precision).
     * Total should add up to 1000 for 100%.
     * @var array<string, int>
     */
    protected array $outcomeWeights = [
        'dark_matter' => 90,      // 9.0% - Find Dark Matter
        'ships' => 220,           // 22.0% - Find abandoned ships
        'resources' => 325,       // 32.5% - Find resources
        // 'pirates' => 58,       // 5.8% - Find pirates (combat) - TODO: implement combat
        // 'aliens' => 26,        // 2.6% - Find aliens (combat) - TODO: implement combat
        'delay' => 70,            // 7.0% - Fleet has delay
        'speedup' => 20,          // 2.0% - Fleet returns early
        'nothing' => 265,         // 26.5% - Find nothing (includes pirates/aliens weight for now)
        'black_hole' => 3,        // 0.33% - Black hole (fleet loss)
        'merchant' => 7,          // 0.7% - Find merchant
    ];

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
                if ($foundUnits->getAmount() > 0) {
                    $units->addCollection($foundUnits);
                } else {
                    // No ships could be granted for this fleet -> treat as Failed
                    $this->processExpeditionFailedOutcome($mission);
                }
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

        // Define weighted delay factors 2,3,5 probability of 89%, 10%, 1%
        $delayFactors = [
            2 => 89,
            3 => 10,
            5 => 1
        ];

        // Calculate total weight and generate random number
        $totalWeight = array_sum($delayFactors);
        $rand = mt_rand(1, $totalWeight);

        // Select multiplier based on cumulative weight
        $cumulativeWeight = 0;
        $selectedMultiplier = 2; // fallback default

        foreach ($delayFactors as $factor => $weight) {
            $cumulativeWeight += $weight;
            if ($rand <= $cumulativeWeight) {
                $selectedMultiplier = $factor;
                break;
            }
        }

        // Calculate base additional return trip time based on holding time
        // Formula: Base Delay = Delay factor × Holding time
        $baseAdditionalReturnTripTime = $mission->time_holding * $selectedMultiplier;

        // Apply universe fleet speed modifier
        // Formula: Actual Delay = Base Delay / Fleet Speed
        $fleetSpeed = $this->settings->fleetSpeed();
        $additionalReturnTripTime = intval($baseAdditionalReturnTripTime / $fleetSpeed);

        // Send a message to the player with the failure and delay outcome
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
        $resourcesFound = new Resources(0, 0, 0, 0);

        // Get max cargo capacity of the fleet.
        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);
        $maxCargoCapacity = $units->getTotalCargoCapacity($player);

        // Determine the max resource find.
        $maxResourceFind = $this->determineMaxResourceFind();

        // Determine the resource type: metal, crystal or deuterium.
        $cargoCapacityConstrainedAmount = 0;
        $resource_type_int = random_int(0, 2);
        switch ($resource_type_int) {
            case 0:
                $resource_type = ResourceType::Metal;

                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $maxResourceFind);
                $resourcesFound->metal->set($cargoCapacityConstrainedAmount);
                break;
            case 1:
                $resource_type = ResourceType::Crystal;

                $adjustedMaxResourceFind = $maxResourceFind * (2 / 3);
                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $adjustedMaxResourceFind);
                $resourcesFound->crystal->set($cargoCapacityConstrainedAmount);
                break;
            case 2:
                $resource_type = ResourceType::Deuterium;

                $adjustedMaxResourceFind = $maxResourceFind * (1 / 3);
                $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $adjustedMaxResourceFind);
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

        // Define expedition hierarchy levels (eligibility + ceiling).
        // TODO: when implementing pathfinder and reaper units, add them to the levels array.
        // Pathfinder = between cruiser and battle_ship, Reaper = after destroyer.
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
        $getShipLevel = function ($shipMachineName) use ($expeditionLevels) {
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

        // Collect all ships that can be found up to one tier above the highest tier present,
        // capped at the highest defined tier.
        $highestDefinedLevel = max(array_keys($expeditionLevels));
        $maxFindLevel = min($maxExpeditionLevel + 1, $highestDefinedLevel);

        $possibleShipMachineNames = [];
        for ($level = 1; $level <= $maxFindLevel; $level++) {
            $possibleShipMachineNames = array_merge($possibleShipMachineNames, $expeditionLevels[$level]);
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

        // If no ships can be found at all for this fleet composition, just return empty.
        // The caller will decide how to message (fallback to Failed).
        if (empty($possibleShips)) {
            return new UnitCollection();
        }

        // Get max cargo capacity of the fleet.
        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);
        $maxCargoCapacity = $units->getTotalCargoCapacity($player);

        // Determine the max resource find.
        $maxResourceFind = $this->determineMaxResourceFind();
        $cargoCapacityConstrainedAmount = min($maxCargoCapacity, $maxResourceFind);

        // Select 1-6 random ship types from possible ships.
        $num_ship_types = min(random_int(1, 6), count($possibleShips));
        shuffle($possibleShips);
        $selectedShips = array_slice($possibleShips, 0, $num_ship_types);

        // Distribute resources per ship type with randomness (up to 75% variance), last ship gets the remainder.
        // E.g. if there are 500k resources to find and 3 ship types, the actual ship amount found might look like this:
        // Ship type 1: 150k / 4k cost per ship = 37 ships (rounded down)
        // Ship type 2: 250k / 10k cost per ship = 25 ships
        // Ship type 3: 100k / 20k cost per ship = 5 ships
        // The distribution is random, so it will look different each time.
        $remainingResources = $cargoCapacityConstrainedAmount;
        $numShips = count($selectedShips);
        $averageResourcePerShip = $cargoCapacityConstrainedAmount / $numShips;

        $units = new UnitCollection();
        foreach ($selectedShips as $i => $ship) {
            $shipPrice = $ship->price->resources->sum();

            if ($i < $numShips - 1) {
                // For all but the last ship, allocate a random portion (25% to 175%) of the average share
                $minResources = $averageResourcePerShip * 0.25;
                $maxResources = $averageResourcePerShip * 1.75;
                $randomResources = min(
                    $remainingResources,
                    random_int((int)round($minResources), (int)round($maxResources))
                );
            } else {
                // Last ship gets all remaining resources
                $randomResources = $remainingResources;
            }

            $amount = (int)floor($randomResources / $shipPrice);
            if ($amount > 0) {
                $units->addUnit($ship, $amount);
            }

            $remainingResources -= $randomResources;
            if ($remainingResources < 0) {
                $remainingResources = 0;
            }
        }

        if (empty($units->units)) {
            $cheapest = $units->findCheapestShip($possibleShips);

            if ($cheapest !== null && $cargoCapacityConstrainedAmount >= $cheapest->price->resources->sum()) {
                $units->addUnit($cheapest, 1);
            }
        }

        // If still empty here (no eligible or affordable ships), return empty.
        // Caller will handle fallback messaging.
        if (empty($units->units)) {
            return new UnitCollection();
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
     * Select a random expedition outcome based on configured weights. Higher weight
     * for a particular outcome means more chance of that outcome being selected
     * relative to the other outcomes.
     *
     * @return ExpeditionOutcomeType
     */
    private function selectRandomOutcome(): ExpeditionOutcomeType
    {
        $settingsService = app(SettingsService::class);

        // Map outcome types to their weights
        $outcomeMapping = [
            'dark_matter' => ExpeditionOutcomeType::GainDarkMatter,
            'ships' => ExpeditionOutcomeType::GainShips,
            'resources' => ExpeditionOutcomeType::GainResources,
            'delay' => ExpeditionOutcomeType::FailedAndDelay,
            'speedup' => ExpeditionOutcomeType::FailedAndSpeedup,
            'nothing' => ExpeditionOutcomeType::Failed,
            'black_hole' => ExpeditionOutcomeType::LossOfFleet,
            'merchant' => ExpeditionOutcomeType::GainMerchantTrade,
        ];

        // Build weighted array of enabled outcomes
        $weightedOutcomes = [];
        $totalWeight = 0;

        foreach ($this->outcomeWeights as $key => $weight) {
            if (!isset($outcomeMapping[$key])) {
                continue;
            }

            $outcome = $outcomeMapping[$key];

            // Check if outcome is enabled in settings
            if ($settingsService->get($outcome->getSettingKey()) === '1') {
                // TODO: Remove this filter once outcomes are fully implemented
                // For now, skip unimplemented outcomes
                if (in_array($outcome, [
                    ExpeditionOutcomeType::GainDarkMatter,
                    ExpeditionOutcomeType::GainItems,
                    ExpeditionOutcomeType::GainMerchantTrade,
                    ExpeditionOutcomeType::Battle,
                ])) {
                    continue;
                }

                $weightedOutcomes[] = [
                    'outcome' => $outcome,
                    'weight' => $weight,
                ];
                $totalWeight += $weight;
            }
        }

        // If no outcomes are enabled, default to failure
        if (empty($weightedOutcomes)) {
            return ExpeditionOutcomeType::Failed;
        }

        // Pick a random number between 1 and total weight
        $random = random_int(1, $totalWeight);

        // Find which outcome was selected
        $currentWeight = 0;
        foreach ($weightedOutcomes as $weighted) {
            $currentWeight += $weighted['weight'];
            if ($random <= $currentWeight) {
                return $weighted['outcome'];
            }
        }

        // Fallback (should never reach here)
        return ExpeditionOutcomeType::Failed;
    }

    /**
     * Get the max resource find based on the rank 1 highscore points and by returning a random
     * value between 10% and 100% of the actual max resource find for variance.
     *
     * This method is used by both gainResources and gainShips outcome.
     *
     * @return int
     */
    private function determineMaxResourceFind(): int
    {
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

        // TODO: when pathfinder unit is added to the game and included in the fleet, the max find should be doubled.

        return (int)$resourceAmount;
    }
}

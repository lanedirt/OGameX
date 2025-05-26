<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = false;

    protected static array $outcomes = [
        // Resources found:
        [
            'success' => true,
            'message' => 'On an isolated planetoid we found some easily accessible resources fields and harvested some successfully.',
            'resources' => new Resources(1, 1, 0, 0),
        ],
        [
            'success' => true,
            'message' => 'Your expedition discovered a small asteroid from which some resources could be harvested.',
            'resources' => new Resources(1, 1, 0, 0),
        ],
        [
            'success' => true,
            'message' => 'Your expedition found an ancient, fully loaded but deserted freighter convoy. Some of the resources could be rescued.',
            'resources' => new Resources(1, 1, 0, 0),
        ],
        // Dark Matter found:
        [
            'success' => true,
            'message' => 'The expedition followed some odd signals to an asteroid. In the asteroids core a small amount of Dark Matter was found. The asteroid was taken and the explorers are attempting to extract the Dark Matter.',
            'resources' => new Resources(0, 0, 0, 0),
        ],
        [
            'success' => true,
            'message' => 'The expedition was able to capture and store some Dark Matter.',
            'resources' => new Resources(0, 0, 0, 0),
        ],
        // Units found:
        [
            'success' => true,
            'message' => 'Our expedition found a planet which was almost destroyed during a certain chain of wars. There are different ships floating around in the orbit. The technicians are trying to repair some of them. Maybe we will also get information about what happened here.',
            'units' => new UnitCollection(),
        ],
        [
            'success' => true,
            'message' => 'We found a deserted pirate station. There are some old ships lying in the hangar. Our technicians are figuring out whether some of them are still useful or not.',
            'units' => new UnitCollection(),
        ],
        [
            'success' => true,
            'message' => 'Your expedition ran into the shipyards of a colony that was deserted eons ago. In the shipyards hangar they discover some ships that could be salvaged. The technicians are trying to get some of them to fly again.',
            'units' => new UnitCollection(),
        ],
        [
            'success' => true,
            'message' => 'We came across the remains of a previous expedition! Our technicians will try to get some of the ships to work again.',
            'units' => new UnitCollection(),
        ],
        // Failures:
        [
            'success' => false,
            'message' => 'Due to a failure in the central computers of the flagship, the expedition mission had to be aborted. Unfortunately as a result of the computer malfunction, the fleet returns home empty handed.',
        ],
        [
            'success' => false,
            'message' => 'Your expedition nearly ran into a neutron stars gravitation field and needed some time to free itself. Because of that a lot of Deuterium was consumed and the expedition fleet had to come back without any results.',
        ],
        [
            'success' => false,
            'message' => 'Your expedition went into a sector full of particle storms. This set the energy stores to overload and most of the ships` main systems crashed. Your mechanics were able to avoid the worst, but the expedition is going to return with a big delay.',
        ],
        [
            'success' => false,
            'message' => 'For unknown reasons the expeditions jump went totally wrong. It nearly landed in the heart of a sun. Fortunately it landed in a known system, but the jump back is going to take longer than thought.',
        ],
        [
            'success' => false,
            'message' => 'A failure in the flagships reactor core nearly destroys the entire expedition fleet. Fortunately the technicians were more than competent and could avoid the worst. The repairs took quite some time and forced the expedition to return without having accomplished its goal.',
        ],
        [
            'success' => false,
            'message' => 'A living being made out of pure energy came aboard and induced all the expedition members into some strange trance, causing them to only gazed at the hypnotizing patterns on the computer screens. When most of them finally snapped out of the hypnotic-like state, the expedition mission needed to be aborted as they had way too little Deuterium.',
        ],
        [
            'success' => false,
            'message' => 'Your expeditions doesn`t report any anomalies in the explored sector. But the fleet ran into some solar wind while returning. This resulted in the return trip being expedited. Your expedition returns home a bit earlier.',
        ],
        [
            'success' => false,
            'message' => 'The new navigation module is still buggy. The expeditions jump not only lead them in the wrong direction, but it used all the Deuterium fuel. Fortunately the fleets jump got them close to the departure planets moon. A bit disappointed the expedition now returns without impulse power. The return trip will take longer than expected.',
        ],
        [
            'success' => false,
            'message' => 'Your expedition has learnt about the extensive emptiness of space. There was not even one small asteroid or radiation or particle that could have made this expedition interesting.',
        ],
        // Failure and battle triggered:
        [
            'success' => false,
            'message' => 'Some primitive barbarians are attacking us with spaceships that can`t even be named as such. If the fire gets serious we will be forced to fire back.',
        ],
        // Failure and fleet destroyed:
        [
            'success' => false,
            'message' => 'A core meltdown of the lead ship leads to a chain reaction, which destroys the entire expedition fleet in a spectacular explosion.',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Expedition mission is only possible for position 16.
        if ($targetCoordinate->position !== 16) {
            return new MissionPossibleStatus(false);
        }

        // Mission is only possible towards a planet.
        if ($targetType === PlanetType::Moon) {
            return new MissionPossibleStatus(false, __('Error, there is no moon'));
        }

        // Only possible if player has astrophysics research level 1 or higher.
        if ($planet->getPlayer()->getResearchLevel('astrophysics') <= 0) {
            return new MissionPossibleStatus(false, __('Fleets cannot be sent to this target. You have to research Astrophysics first.'));
        }

        if ($targetType === PlanetType::DebrisField) {
            // TODO: this logic should check if there are actually pathfinders in the units collection
            // once the pathfinder unit has been added to the game.
            return new MissionPossibleStatus(false, __('Pathfinders must be sent for recycling!'));
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

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);

        // Create and start the return mission.
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
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

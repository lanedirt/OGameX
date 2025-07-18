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
use Exception;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = false;

    /**
     * Returns a list of possible outcomes for an expedition.
     * @return array<array{success: bool, message: string, resources?: Resources, units?: UnitCollection}>
     */
    protected static function getOutcomes(): array
    {
        return [
            // Resources found:
            [
                'success' => true,
                'message' => 'On an isolated planetoid we found some easily accessible resources fields and harvested some successfully.',
                // TODO: some messages have "Entry from the communications officers logbook: It seems that this part of the universe has not been explored yet." appended to it, this one too.
                // TODO2: "Entry from the communications officers logbook: It feels great to be the first ones traveling through an unexplored sector."
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
            [
                'success' => true,
                'message' => 'Your expedition fleet reports the discovery of a giant alien ship wreck. They were not able to learn from their technologies but they were able to divide the ship into its main components and made some useful resources out of it.',
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'On a tiny moon with its own atmosphere your expedition found some huge raw resources storage. The crew on the ground is trying to lift and load that natural treasure.',
                'resources' => new Resources(1, 1, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'Mineral belts around an unknown planet contained countless resources. The expedition ships are coming back and their storages are full!',
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
            [
                'success' => true,
                'message' => 'We met an odd alien on the shelf of a small ship who gave us a case with Dark Matter in exchange for some simple mathematical calculations.',
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'We found the remains of an alien ship. We found a little container with some Dark Matter on a shelf in the cargo hold!',
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'Our expedition made first contact with a special race. It looks as though a creature made of pure energy, who named himself Legorian, flew through the expedition ships and then decided to help our underdeveloped species. A case containing Dark Matter materialized at the bridge of the ship!',
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'Our expedition took over a ghost ship which was transporting a small amount of Dark Matter. We didn`t find any hints of what happened to the original crew of the ship, but our technicians where able to rescue the Dark Matter.',
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'Our expedition accomplished a unique experiment. They were able to harvest Dark Matter from a dying star.',
                'resources' => new Resources(0, 0, 0, 0),
            ],
            [
                'success' => true,
                'message' => 'Our Expedition located a rusty space station, which seemed to have been floating uncontrolled through outer space for a long time. The station itself was totally useless, however, it was discovered that some Dark Matter is stored in the reactor. Our technicians are trying to save as much as they can.',
                'resources' => new Resources(1, 1, 0, 0),
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
            [
                'success' => true,
                'message' => 'Our expedition ran into an old automatic shipyard. Some of the ships are still in the production phase and our technicians are currently trying to reactivate the yards energy generators.',
                'units' => new UnitCollection(),
            ],
            [
                'success' => true,
                'message' => 'We found the remains of an armada. The technicians directly went to the almost intact ships to try to get them to work again.',
                'units' => new UnitCollection(),
            ],
            [
                'success' => true,
                'message' => 'We found the planet of an extinct civilization. We are able to see a giant intact space station, orbiting. Some of your technicians and pilots went to the surface looking for some ships which could still be used.',
                'units' => new UnitCollection(),
            ],
            // Items found (TODO: add items to the game)
            [
                'success' => true,
                'message' => 'A fleeing fleet left an item behind, in order to distract us in aid of their escape.',
                //'items' => new ItemCollection(),
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
                'message' => 'The new navigation module is still buggy. The expeditions jump not only lead them in the wrong direction, but it used all the Deuterium fuel. Fortunately the fleets jump got them close to the departure planets moon. A bit disappointed the expedition now returns without impulse power. The return trip will take longer than expected.',
            ],
            [
                'success' => false,
                'message' => 'Your expedition has learnt about the extensive emptiness of space. There was not even one small asteroid or radiation or particle that could have made this expedition interesting.',
            ],
            [
                'success' => false,
                'message' => 'Well, now we know that those red, class 5 anomalies do not only have chaotic effects on the ships navigation systems but also generate massive hallucination on the crew. The expedition didn`t bring anything back.',
            ],
            [
                'success' => false,
                'message' => 'Your expedition fleet made contact with a friendly alien race. They announced that they would send a representative with goods to trade to your worlds.',
            ],
            [
                'success' => false,
                'message' => 'Your expedition took gorgeous pictures of a super nova. Nothing new could be obtained from the expedition, but at least there is good chance to win that "Best Picture Of The Universe" competition in next months issue of OGame magazine.',
            ],
            [
                'success' => false,
                'message' => 'Your expedition fleet followed odd signals for some time. At the end they noticed that those signals where being sent from an old probe which was sent out generations ago to greet foreign species. The probe was saved and some museums of your home planet already voiced their interest.',
            ],
            [
                'success' => false,
                'message' => 'Despite the first, very promising scans of this sector, we unfortunately returned empty handed.',
            ],
            [
                'success' => false,
                'message' => 'Besides some quaint, small pets from a unknown marsh planet, this expedition brings nothing thrilling back from the trip.',
            ],
            [
                'success' => false,
                'message' => 'The expedition`s flagship collided with a foreign ship when it jumped into the fleet without any warning. The foreign ship exploded and the damage to the flagship was substantial. The expedition cannot continue in these conditions, and so the fleet will begin to make its way back once the needed repairs have been carried out.',
            ],
            [
                'success' => false,
                'message' => 'We found the remains of an alien ship. We found a little container with some Dark Matter on a shelf in the cargo hold!',
            ],
            [
                'success' => false,
                'message' => 'Our expedition team came across a strange colony that had been abandoned eons ago. After landing, our crew started to suffer from a high fever caused by an alien virus. It has been learned that this virus wiped out the entire civilization on the planet. Our expedition team is heading home to treat the sickened crew members. Unfortunately we had to abort the mission and we come home empty handed.',
            ],
            [
                'success' => false,
                'message' => 'A strange computer virus attacked the navigation system shortly after parting our home system. This caused the expedition fleet to fly in circles. Needless to say that the expedition wasn`t really successful.',
            ],
            [
            // Failure (and speed up?)
                'success' => false,
                'message' => 'Your expeditions doesn`t report any anomalies in the explored sector. But the fleet ran into some solar wind while returning. This resulted in the return trip being expedited. Your expedition returns home a bit earlier.',
            ],
            [
                'success' => false,
                'message' => 'The new and daring commander successfully traveled through an unstable wormhole to shorten the flight back! However, the expedition itself didn`t bring anything new.',
            ],
            [
                'success' => false,
                'message' => 'An unexpected back coupling in the energy spools of the engines hastened the expeditions return, it returns home earlier than expected. First reports tell they do not have anything thrilling to account for.',
            ],
            // Failure (and delay?)
            [
                'success' => false,
                'message' => 'Your expedition went into a sector full of particle storms. This set the energy stores to overload and most of the ships` main systems crashed. Your mechanics were able to avoid the worst, but the expedition is going to return with a big delay.',
            ],
            [
                'success' => false,
                'message' => 'Your navigator made a grave error in his computations that caused the expeditions jump to be miscalculated. Not only did the fleet miss the target completely, but the return trip will take a lot more time than originally planned.',
            ],
            [
                'success' => false,
                'message' => 'The solar wind of a red giant ruined the expeditions jump and it will take quite some time to calculate the return jump. There was nothing besides the emptiness of space between the stars in that sector. The fleet will return later than expected.',
            ],
            // Failure and battle triggered:
            [
                'success' => false,
                'message' => 'Some primitive barbarians are attacking us with spaceships that can`t even be named as such. If the fire gets serious we will be forced to fire back.',
            ],
            [
                'success' => false,
                'message' => 'We needed to fight some pirates which were, fortunately, only a few.',
            ],
            [
                'success' => false,
                'message' => 'We caught some radio transmissions from some drunk pirates. Seems like we will be under attack soon.',
            ],
            [
                'success' => false,
                'message' => 'Our expedition was attacked by a small group of unknown ships!',
            ],
            [
                'success' => false,
                'message' => 'Some really desperate space pirates tried to capture our expedition fleet.',
            ],
            [
                'success' => false,
                'message' => 'Some exotic looking ships attacked the expedition fleet without warning!',
            ],
            [
                'success' => false,
                'message' => 'Your expedition fleet had an unfriendly first contact with an unknown species.',
            ],
            // Failure and fleet destroyed:
            [
                'success' => false,
                'message' => 'A core meltdown of the lead ship leads to a chain reaction, which destroys the entire expedition fleet in a spectacular explosion.',
            ],
        ];
    }

    /**
     * Override the parent method to add expedition-specific mission sanity checks that run just before a mission is started.
     *
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param PlanetType $targetType
     * @param UnitCollection $units
     * @param Resources $resources
     * @return void
     * @throws Exception
     */
    public function startMissionSanityChecks(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units, Resources $resources): void
    {
        parent::startMissionSanityChecks($planet, $targetCoordinate, $targetType, $units, $resources);

        // Check if there are enough expedition slots available.
        if ($planet->getPlayer()->getExpeditionSlotsInUse() >= $planet->getPlayer()->getExpeditionSlotsMax()) {
            throw new Exception('You are conducting too many expeditions at the same time.');
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

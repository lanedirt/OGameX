<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\StationObject;
use OGame\Models\Enums\PlanetType;

class StationObjects
{
    /**
     * Returns all station (facilities) objects.
     *
     * @return array<StationObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Robotics Factory ---
        $roboticsFactory = new StationObject();
        $roboticsFactory->id = 14;
        $roboticsFactory->title = 'Robotics Factory';
        $roboticsFactory->machine_name = 'robot_factory';
        $roboticsFactory->class_name = 'roboticsFactory';
        $roboticsFactory->description = 'Robotic factories provide construction robots to aid in the construction of buildings. Each level increases the speed of the upgrade of buildings.';
        $roboticsFactory->description_long = 'The Robotics Factory primary goal is the production of State of the Art construction robots. Each upgrade to the robotics factory results in the production of faster robots, which is used to reduce the time needed to construct buildings.';
        $roboticsFactory->price = new GameObjectPrice(400, 120, 200, 0, 2);
        $roboticsFactory->assets = new GameObjectAssets();
        $roboticsFactory->assets->imgMicro = 'robot_factory_micro.jpg';
        $roboticsFactory->assets->imgSmall = 'robot_factory_small.jpg';

        $buildingObjectsNew[] = $roboticsFactory;

        // --- Shipyard ---
        $shipyard = new StationObject();
        $shipyard->id = 21;
        $shipyard->title = 'Shipyard';
        $shipyard->machine_name = 'shipyard';
        $shipyard->class_name = 'shipyard';
        $shipyard->description = 'All types of ships and defensive facilities are built in the planetary shipyard.';
        $shipyard->description_long = 'The planetary shipyard is responsible for the construction of spacecraft and defensive mechanisms. As the shipyard is upgraded, it can produce a wider variety of vehicles at a much greater rate of speed. If a nanite factory is present on the planet, the speed at which ships are constructed is massively increased.';
        $shipyard->requirements = [
            new GameObjectRequirement('robot_factory', 2),
        ];
        $shipyard->price = new GameObjectPrice(400, 200, 100, 0, 2);
        $shipyard->assets = new GameObjectAssets();
        $shipyard->assets->imgMicro = 'shipyard_micro.jpg';
        $shipyard->assets->imgSmall = 'shipyard_small.jpg';

        $buildingObjectsNew[] = $shipyard;

        // --- Research Lab ---
        $researchLab = new StationObject();
        $researchLab->id = 31;
        $researchLab->title = 'Research Lab';
        $researchLab->machine_name = 'research_lab';
        $researchLab->class_name = 'researchLaboratory';
        $researchLab->description = 'A research lab is required in order to conduct research into new technologies.';
        $researchLab->description_long = 'An essential part of any empire, Research Labs are where new technologies are discovered and older technologies are improved upon. With each level of the Research Lab constructed, the speed in which new technologies are researched is increased, while also unlocking newer technologies to research. In order to conduct research as quickly as possible, research scientists are immediately dispatched to the colony to begin work and development. In this way, knowledge about new technologies can easily be disseminated throughout the empire.';
        $researchLab->price = new GameObjectPrice(200, 400, 200, 0, 2);
        $researchLab->valid_planet_types = [PlanetType::Planet];
        $researchLab->assets = new GameObjectAssets();
        $researchLab->assets->imgMicro = 'research_lab_micro.jpg';
        $researchLab->assets->imgSmall = 'research_lab_small.jpg';

        $buildingObjectsNew[] = $researchLab;

        // --- Alliance Depot ---
        $allianceDepot = new StationObject();
        $allianceDepot->id = 34;
        $allianceDepot->title = 'Alliance Depot';
        $allianceDepot->machine_name = 'alliance_depot';
        $allianceDepot->class_name = 'allianceDepot';
        $allianceDepot->description = 'The alliance depot supplies fuel to friendly fleets in orbit helping with defense.';
        $allianceDepot->description_long = 'The alliance depot supplies fuel to friendly fleets in orbit helping with defense. For each upgrade level of the alliance depot, a special demand of deuterium per hour can be sent to an orbiting fleet.';
        $allianceDepot->price = new GameObjectPrice(20000, 40000, 0, 0, 2);
        $allianceDepot->valid_planet_types = [PlanetType::Planet];
        $allianceDepot->assets = new GameObjectAssets();
        $allianceDepot->assets->imgMicro = 'alliance_depot_micro.jpg';
        $allianceDepot->assets->imgSmall = 'alliance_depot_small.jpg';

        $buildingObjectsNew[] = $allianceDepot;

        // --- Missile Silo ---
        $missileSilo = new StationObject();
        $missileSilo->id = 44;
        $missileSilo->title = 'Missile Silo';
        $missileSilo->machine_name = 'missile_silo';
        $missileSilo->class_name = 'missileSilo';
        $missileSilo->description = 'Missile silos are used to store missiles.';
        $missileSilo->description_long = 'Missile silos are used to construct, store and launch interplanetary and anti-ballistic missiles. With each level of the silo, five interplanetary missiles or ten anti-ballistic missiles can be stored. One Interplanetary missile uses the same space as two Anti-Ballistic missiles. Storage of both Interplanetary missiles and Anti-Ballistic missiles in the same silo is allowed.';
        $missileSilo->requirements = [
            new GameObjectRequirement('shipyard', 1)
        ];
        $missileSilo->price = new GameObjectPrice(20000, 20000, 1000, 0, 2);
        $missileSilo->assets = new GameObjectAssets();
        $missileSilo->assets->imgMicro = 'missile_silo_micro.jpg';
        $missileSilo->assets->imgSmall = 'missile_silo_small.jpg';

        $buildingObjectsNew[] = $missileSilo;

        // --- Nanite Factory ---
        $naniteFactory = new StationObject();
        $naniteFactory->id = 15;
        $naniteFactory->title = 'Nanite Factory';
        $naniteFactory->machine_name = 'nano_factory';
        $naniteFactory->class_name = 'naniteFactory';
        $naniteFactory->description = 'This is the ultimate in robotics technology. Each level cuts the construction time for buildings, ships, and defenses.';
        $naniteFactory->description_long = 'A nanomachine, also called a nanite, is a mechanical or electromechanical device whose dimensions are measured in nanometers (millionths of a millimeter, or units of 10^-9 meter). The microscopic size of nanomachines translates into higher operational speed. This factory produces nanomachines that are the ultimate evolution in robotics technology. Once constructed, each upgrade significantly decreases production time for buildings, ships, and defensive structures.';
        $naniteFactory->requirements = [
            new GameObjectRequirement('computer_technology', 10),
            new GameObjectRequirement('robot_factory', 10),
        ];
        $naniteFactory->price = new GameObjectPrice(1000000, 500000, 100000, 0, 2);
        $naniteFactory->valid_planet_types = [PlanetType::Planet];
        $naniteFactory->assets = new GameObjectAssets();
        $naniteFactory->assets->imgMicro = 'nanite_factory_micro.jpg';
        $naniteFactory->assets->imgSmall = 'nanite_factory_small.jpg';

        $buildingObjectsNew[] = $naniteFactory;

        // --- Terraformer ---
        $terraformer = new StationObject();
        $terraformer->id = 33;
        $terraformer->title = 'Terraformer';
        $terraformer->machine_name = 'terraformer';
        $terraformer->class_name = 'terraformer';
        $terraformer->description = 'The terraformer increases the usable surface of planets.';
        $terraformer->description_long = 'With the increasing construction on planets, even the living space for the colony is becoming more and more limited. Traditional methods such as high-rise and underground construction are increasingly becoming insufficient. A small group of high-energy physicists and nano engineers eventually came to the solution: terraforming.
Making use of tremendous amounts of energy, the terraformer can make whole stretches of land or even continents arable. This building houses the production of nanites created specifically for this purpose, which ensure a consistent ground quality throughout.


Each terraformer level allows 5 fields to be cultivated. With each level, the terraformer occupies one field itself. Every 2 terraformer levels you will receive 1 bonus field.

Once built, the terraformer cannot be dismantled.';
        $terraformer->requirements = [
            new GameObjectRequirement('nano_factory', 1),
            new GameObjectRequirement('energy_technology', 12),
        ];
        $terraformer->price = new GameObjectPrice(50000, 0, 100000, 1000, 2);
        $terraformer->valid_planet_types = [PlanetType::Planet];
        $terraformer->assets = new GameObjectAssets();
        $terraformer->assets->imgMicro = 'terraformer_micro.jpg';
        $terraformer->assets->imgSmall = 'terraformer_small.jpg';

        $buildingObjectsNew[] = $terraformer;

        // --- Space Dock ---
        $spaceDock = new StationObject();
        $spaceDock->id = 36;
        $spaceDock->title = 'Space Dock';
        $spaceDock->machine_name = 'space_dock';
        $spaceDock->class_name = 'repairDock';
        $spaceDock->description = 'Wreckages can be repaired in the Space Dock.';
        $spaceDock->description_long = 'The Space Dock offers the possibility to repair ships destroyed in battle which left behind wreckage. The repair time takes a maximum of 12 hours, but it takes at least 30 minutes until the ships can be put back into service.

Repairs must begin within 3 days of the creation of the wreckage. The repaired ships must be returned to duty manually after completion of the repairs. If this is not done, individual ships of any type will be returned to service after 3 days.

Wreckage only appears if more than 150,000 units have been destroyed including one’s own ships which took part in the combat with a value of at least 5% of the ship points.

Since the Space Dock floats in orbit, it does not require a planet field.';
        $spaceDock->requirements = [
            new GameObjectRequirement('shipyard', 2),
        ];
        $spaceDock->price = new GameObjectPrice(200, 0, 50, 50, 2);
        $spaceDock->valid_planet_types = [PlanetType::Planet];
        $spaceDock->assets = new GameObjectAssets();
        $spaceDock->assets->imgMicro = 'space_dock_micro.jpg';
        $spaceDock->assets->imgSmall = 'space_dock_small.jpg';

        $buildingObjectsNew[] = $spaceDock;

        // --- Lunar Base ---
        $lunarBase = new StationObject();
        $lunarBase->id = 41;
        $lunarBase->title = 'Lunar Base';
        $lunarBase->machine_name = 'lunar_base';
        $lunarBase->class_name = 'moonbase';
        $lunarBase->description = 'Since the moon has no atmosphere, a lunar base is required to generate habitable space.';
        $lunarBase->description_long = 'A moon has no atmosphere, so a lunar base must first be built before a settlement can be set up. This then provides oxygen, heating, and gravity. With each level constructed, a larger living and development area is provided within the biosphere. Each constructed level allows three fields for other buildings. With each level, the Lunar base occupies one field itself.
Once built, the lunar base can not be torn down.';
        $lunarBase->price = new GameObjectPrice(20000, 40000, 20000, 0, 2);
        $lunarBase->valid_planet_types = [PlanetType::Moon];
        $lunarBase->assets = new GameObjectAssets();
        $lunarBase->assets->imgMicro = 'lunar_base_micro.jpg';
        $lunarBase->assets->imgSmall = 'lunar_base_small.jpg';

        $buildingObjectsNew[] = $lunarBase;

        // --- Sensor Phalanx ---
        $sensorPhalanx = new StationObject();
        $sensorPhalanx->id = 42;
        $sensorPhalanx->title = 'Sensor Phalanx';
        $sensorPhalanx->machine_name = 'sensor_phalanx';
        $sensorPhalanx->class_name = 'sensorPhalanx';
        $sensorPhalanx->description = 'Using the sensor phalanx, fleets of other empires can be discovered and observed. The bigger the sensor phalanx array, the larger the range it can scan.';
        $sensorPhalanx->description_long = 'Utilizing high-resolution sensors, the Sensor Phalanx first scans the spectrum of light, composition of gases, and radiation emissions from a distant world and transmits the data to a supercomputer for processing. Once the information is obtained, the supercomputer compares changes in the spectrum, gas composition, and radiation emissions, to a base line chart of known changes of the spectrum created by various ship movements. The resulting data then displays activity of any fleet within the range of the phalanx. To prevent the supercomputer from overheating during the process, it is cooled by utilizing 5k of processed Deuterium.
To use the Phalanx, click on any planet in the Galaxy View within your sensors range.';
        $sensorPhalanx->requirements = [
            new GameObjectRequirement('lunar_base', 1),
        ];
        $sensorPhalanx->price = new GameObjectPrice(20000, 40000, 10000, 0, 2);
        $sensorPhalanx->valid_planet_types = [PlanetType::Moon];
        $sensorPhalanx->assets = new GameObjectAssets();
        $sensorPhalanx->assets->imgMicro = 'sensor_phalanx_micro.jpg';
        $sensorPhalanx->assets->imgSmall = 'sensor_phalanx_small.jpg';

        $buildingObjectsNew[] = $sensorPhalanx;

        // --- Jump Gate ---
        $jumpGate = new StationObject();
        $jumpGate->id = 43;
        $jumpGate->title = 'Jump Gate';
        $jumpGate->machine_name = 'jump_gate';
        $jumpGate->class_name = 'jumpGate';
        $jumpGate->description = 'Jump gates are huge transceivers capable of sending even the biggest fleet in no time to a distant jump gate.';
        $jumpGate->description_long = 'A Jump Gate is a system of giant transceivers capable of sending even the largest fleets to a receiving Gate anywhere in the universe without loss of time. Utilizing technology similar to that of a Worm Hole to achieve the jump, deuterium is not required. A recharge period of a few minutes must pass between jumps to allow for regeneration. Transporting resources through the Gate is not possible either. With every upgrade level the jump gate`s cooldown time can be reduced.';
        $jumpGate->requirements = [
            new GameObjectRequirement('lunar_base', 1),
            new GameObjectRequirement('hyperspace_technology', level: 7),
        ];
        $jumpGate->price = new GameObjectPrice(2000000, 4000000, 2000000, 0, 2);
        $jumpGate->valid_planet_types = [PlanetType::Moon];
        $jumpGate->assets = new GameObjectAssets();
        $jumpGate->assets->imgMicro = 'jump_gate_micro.jpg';
        $jumpGate->assets->imgSmall = 'jump_gate_small.jpg';

        $buildingObjectsNew[] = $jumpGate;

        return $buildingObjectsNew;
    }
}

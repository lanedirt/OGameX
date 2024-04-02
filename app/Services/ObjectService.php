<?php

namespace OGame\Services;

use OGame\Facades\AppUtil;

/**
 * Class ObjectService.
 *
 * Contains all information about game objects such as buildings, research,
 * ships, defense etc.
 *
 * @package OGame\Services
 */
class ObjectService
{
    /**
     * Buildings.
     */
    protected $buildingObjects;

    /**
     * Station.
     */
    protected $stationObjects;

    /**
     * Research.
     */
    protected $researchObjects;

    /**
     * Military ships.
     */
    protected $militaryShipObjects;

    /**
     * Civil ships.
     */
    protected $civilShipObjects;

    /**
     * Defense.
     */
    protected $defenseObjects;

    /**
     * ObjectService constructor.
     */
    public function __construct()
    {
        $this->buildingObjects = [
            1 => [
                'id' => 1,
                'type' => 'building',
                'title' => 'Metal Mine',
                'machine_name' => 'metal_mine',
                'description' => 'Used in the extraction of metal ore, metal mines are of primary importance to all emerging and established empires.',
                'description_long' => 'Metal is the primary resource used in the foundation of your Empire. At greater depths, the mines can produce more output of viable metal for use in the construction of buildings, ships, defense systems, and research. As the mines drill deeper, more energy is required for maximum production. As metal is the most abundant of all resources available, its value is considered to be the lowest of all resources for trading.',
                'requirements' => [],
                'price' => [
                    'metal' => 60,
                    'crystal' => 15,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 1.5,
                ],
                'production' => [
                    'metal' => 'return (30 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                    'crystal' => 'return "0";',
                    'deuterium' => 'return "0";',
                    'energy' => 'return - (10 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'metal_mine_small.jpg',
                        'micro' => 'metal_mine_micro.jpg',
                    ],
                ],
            ],
            2 => [
                'id' => 2,
                'type' => 'building',
                'title' => 'Crystal Mine',
                'machine_name' => 'crystal_mine',
                'description' => 'Crystals are the main resource used to build electronic circuits and form certain alloy compounds.',
                'description_long' => 'Crystal mines supply the main resource used to produce electronic circuits and from certain alloy compounds. Mining crystal consumes some one and half times more energy than a mining metal, making crystal more valuable. Almost all ships and all buildings require crystal. Most crystals required to build spaceships, however, are very rare, and like metal can only be found at a certain depth. Therefore, building mines in deeper strata will increase the amount of crystal produced.',
                'requirements' => [],
                'price' => [
                    'metal' => 48,
                    'crystal' => 24,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 1.6,
                ],
                'production' => [
                    'metal' => 'return "0";',
                    'crystal' => 'return (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                    'deuterium' => 'return "0";',
                    'energy' => 'return - (10 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'crystal_mine_small.jpg',
                        'micro' => 'crystal_mine_micro.jpg',
                    ],
                ],
            ],
            3 => [
                'id' => 3,
                'type' => 'building',
                'title' => 'Deuterium Synthesizer',
                'machine_name' => 'deuterium_synthesizer',
                'description' => 'Deuterium Synthesizers draw the trace Deuterium content from the water on a planet.',
                'description_long' => 'Deuterium is also called heavy hydrogen. It is a stable isotope of hydrogen with a natural abundance in the oceans of colonies of approximately one atom in 6500 of hydrogen (~154 PPM). Deuterium thus accounts for approximately 0.015% (on a weight basis, 0.030%) of all. Deuterium is processed by special synthesizers which can separate the water from the Deuterium using specially designed centrifuges. The upgrade of the synthesizer allows for increasing the amount of Deuterium deposits processed. Deuterium is used when carrying out sensor phalanx scans, viewing galaxies, as fuel for ships, and performing specialized research upgrades.',
                'requirements' => [],
                'price' => [
                    'metal' => 225,
                    'crystal' => 75,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 1.5,
                ],
                'production' => [
                    'metal' => 'return "0";',
                    'crystal' => 'return "0";',
                    'deuterium' => 'return ((10 * $building_level * pow((1.1), $building_level)) * (-0.002 * $planet_temperature + 1.28))  * (0.1 * $building_percentage);',
                    'energy' => 'return - (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'deuterium_synthesizer_small.jpg',
                        'micro' => 'deuterium_synthesizer_micro.jpg',
                    ],
                ],
            ],
            4 => [
                'id' => 4,
                'type' => 'building',
                'title' => 'Solar Plant',
                'machine_name' => 'solar_plant',
                'description' => 'Solar power plants absorb energy from solar radiation. All mines need energy to operate.',
                'description_long' => 'Gigantic solar arrays are used to generate power for the mines and the deuterium synthesizer. As the solar plant is upgraded, the surface area of the photovoltaic cells covering the planet increases, resulting in a higher energy output across the power grids of your planet.',
                'requirements' => [],
                'price' => [
                    'metal' => 75,
                    'crystal' => 30,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 1.5,
                ],
                'production' => [
                    'metal' => 'return "0";',
                    'crystal' => 'return "0";',
                    'deuterium' => 'return "0";',
                    'energy' => 'return (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'solar_plant_small.jpg',
                        'micro' => 'solar_plant_micro.jpg',
                    ],
                ],
            ],
            12 => [
                'id' => 12,
                'type' => 'building',
                'title' => 'Fusion Reactor',
                'machine_name' => 'fusion_plant',
                'description' => 'The fusion reactor uses deuterium to produce energy.',
                'description_long' => 'In fusion power plants, hydrogen nuclei are fused into helium nuclei under enormous temperature and pressure, releasing tremendous amounts of energy. For each gram of Deuterium consumed, up to 41,32*10^-13 Joule of energy can be produced; with 1 g you are able to produce 172 MWh energy.

Larger reactor complexes use more deuterium and can produce more energy per hour. The energy effect could be increased by researching energy technology.

The energy production of the fusion plant is calculated like that:
30 * [Level Fusion Plant] * (1,05 + [Level Energy Technology] * 0,01) ^ [Level Fusion Plant]',
                'requirements' => [3 => 5, 113 => 3],
                'price' => [
                    'metal' => 900,
                    'crystal' => 360,
                    'deuterium' => 180,
                    'energy' => 0,
                    'factor' => 1.8,
                ],
                'production' => [
                    'metal' => 'return "0";',
                    'crystal' => 'return "0";',
                    'deuterium' => 'return - (10 * $building_level * pow(1.1, $building_level));',
                    'energy' => 'return (30 * $building_level * pow((1.05 + $energy_technology_level * 0.01), $building_level)) * (0.1 * $building_percentage);',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'fusion_plant_small.jpg',
                        'micro' => 'fusion_plant_micro.jpg',
                    ],
                ],
            ],
            // Resources -- storage
            22 => [
                'id' => 22,
                'type' => 'building',
                'title' => 'Metal Storage',
                'machine_name' => 'metal_store',
                'description' => 'Provides storage for excess metal.',
                'description_long' => 'This giant storage facility is used to store metal ore. Each level of upgrading increases the amount of metal ore that can be stored. If the stores are full, no further metal will be mined.

        The Metal Storage protects a certain percentage of the mine`s daily production (max. 10 percent).',
                'requirements' => [],
                'price' => [
                    'metal' => 1000,
                    'crystal' => 0,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'storage' => [
                    'metal' => 'return  5000 * floor(2.5 * exp(20 * $building_level / 33));',
                    'crystal' => 'return   "0";',
                    'deuterium' => 'return  "0";',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'metal_store_small.jpg',
                        'micro' => 'metal_store_micro.jpg',
                    ],
                ],
            ],
            23 => [
                'id' => 23,
                'type' => 'building',
                'title' => 'Crystal Storage',
                'machine_name' => 'crystal_store',
                'description' => 'Provides storage for excess crystal.',
                'description_long' => 'The unprocessed crystal will be stored in these giant storage halls in the meantime. With each level of upgrade, it increases the amount of crystal can be stored. If the crystal stores are full, no further crystal will be mined.

The Crystal Storage protects a certain percentage of the mine`s daily production (max. 10 percent).',
                'requirements' => [],
                'price' => [
                    'metal' => 1000,
                    'crystal' => 500,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'storage' => [
                    'metal' => 'return  "0";',
                    'crystal' => 'return  5000 * floor(2.5 * exp(20 * $building_level / 33));',
                    'deuterium' => 'return  "0";',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'crystal_store_small.jpg',
                        'micro' => 'crystal_store_micro.jpg',
                    ],
                ],
            ],
            24 => [
                'id' => 24,
                'type' => 'building',
                'title' => 'Deuterium Tank',
                'machine_name' => 'deuterium_store',
                'description' => 'Giant tanks for storing newly-extracted deuterium.',
                'description_long' => 'The Deuterium tank is for storing newly-synthesized deuterium. Once it is processed by the synthesizer, it is piped into this tank for later use. With each upgrade of the tank, the total storage capacity is increased. Once the capacity is reached, no further Deuterium will be synthesized.

The Deuterium Tank protects a certain percentage of the synthesizer`s daily production (max. 10 percent).',
                'requirements' => [],
                'price' => [
                    'metal' => 1000,
                    'crystal' => 1000,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'storage' => [
                    'metal' => 'return  "0";',
                    'crystal' => 'return   "0";',
                    'deuterium' => 'return  5000 * floor(2.5 * exp(20 * $building_level / 33));',
                ],
                'assets' => [
                    'img' => [
                        'small' => 'deuterium_store_small.jpg',
                        'micro' => 'deuterium_store_micro.jpg',
                    ],
                ],
            ],
        ];

        $this->stationObjects = [
            // Facilities
            14 => [
                'id' => 14,
                'type' => 'station',
                'title' => 'Robotics Factory',
                'machine_name' => 'robot_factory',
                'description' => 'Robotic factories provide construction robots to aid in the construction of buildings. Each level increases the speed of the upgrade of buildings.',
                'description_long' => 'The Robotics Factory primary goal is the production of State of the Art construction robots. Each upgrade to the robotics factory results in the production of faster robots, which is used to reduce the time needed to construct buildings.',
                'requirements' => [],
                'price' => [
                    'metal' => 400,
                    'crystal' => 120,
                    'deuterium' => 200,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            21 => [
                'id' => 21,
                'type' => 'station',
                'title' => 'Shipyard',
                'machine_name' => 'shipyard',
                'description' => 'All types of ships and defensive facilities are built in the planetary shipyard.',
                'description_long' => 'The planetary shipyard is responsible for the construction of spacecraft and defensive mechanisms. As the shipyard is upgraded, it can produce a wider variety of vehicles at a much greater rate of speed. If a nanite factory is present on the planet, the speed at which ships are constructed is massively increased.',
                'requirements' => [14 => 2],
                'price' => [
                    'metal' => 400,
                    'crystal' => 100,
                    'deuterium' => 200,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'shipyard_small.jpg',
                        'micro' => 'shipyard_micro.jpg',
                    ],
                ],
            ],
            31 => [
                'id' => 31,
                'type' => 'station',
                'title' => 'Research Lab',
                'machine_name' => 'research_lab',
                'description' => 'A research lab is required in order to conduct research into new technologies.',
                'description_long' => 'An essential part of any empire, Research Labs are where new technologies are discovered and older technologies are improved upon. With each level of the Research Lab constructed, the speed in which new technologies are researched is increased, while also unlocking newer technologies to research. In order to conduct research as quickly as possible, research scientists are immediately dispatched to the colony to begin work and development. In this way, knowledge about new technologies can easily be disseminated throughout the empire.',
                'requirements' => [],
                'price' => [
                    'metal' => 200,
                    'crystal' => 400,
                    'deuterium' => 200,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'research_lab_small.jpg',
                        'micro' => 'research_lab_micro.jpg',
                    ],
                ],
            ],
            34 => [
                'id' => 34,
                'type' => 'station',
                'title' => 'Alliance Depot',
                'machine_name' => 'alliance_depot',
                'description' => 'The alliance depot supplies fuel to friendly fleets in orbit helping with defense.',
                'description_long' => 'The alliance depot supplies fuel to friendly fleets in orbit helping with defense. For each upgrade level of the alliance depot, a special demand of deuterium per hour can be sent to an orbiting fleet.',
                'requirements' => [],
                'price' => [
                    'metal' => 20000,
                    'crystal' => 40000,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'alliance_depot_small.jpg',
                        'micro' => 'alliance_depot_micro.jpg',
                    ],
                ],
            ],
            44 => [
                'id' => 44,
                'type' => 'station',
                'title' => 'Missile Silo',
                'machine_name' => 'missile_silo',
                'description' => 'Missile silos are used to store missiles.',
                'description_long' => 'Missile silos are used to construct, store and launch interplanetary and anti-ballistic missiles. With each level of the silo, five interplanetary missiles or ten anti-ballistic missiles can be stored. One Interplanetary missile uses the same space as two Anti-Ballistic missiles. Storage of both Interplanetary missiles and Anti-Ballistic missiles in the same silo is allowed.',
                'requirements' => [22 => 1],
                'price' => [
                    'metal' => 20000,
                    'crystal' => 20000,
                    'deuterium' => 1000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'missile_silo_small.jpg',
                        'micro' => 'missile_silo_micro.jpg',
                    ],
                ],
            ],
            15 => [
                'id' => 15,
                'type' => 'station',
                'title' => 'Nanite Factory',
                'machine_name' => 'nanite_factory',
                'description' => 'This is the ultimate in robotics technology. Each level cuts the construction time for buildings, ships, and defenses.',
                'description_long' => 'A nanomachine, also called a nanite, is a mechanical or electromechanical device whose dimensions are measured in nanometers (millionths of a millimeter, or units of 10^-9 meter). The microscopic size of nanomachines translates into higher operational speed. This factory produces nanomachines that are the ultimate evolution in robotics technology. Once constructed, each upgrade significantly decreases production time for buildings, ships, and defensive structures.',
                'requirements' => [14 => 10, 108 => 10],
                'price' => [
                    'metal' => 1000000,
                    'crystal' => 500000,
                    'deuterium' => 100000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'nanite_factory_small.jpg',
                        'micro' => 'nanite_factory_micro.jpg',
                    ],
                ],
            ],
            33 => [
                'id' => 33,
                'type' => 'station',
                'title' => 'Terraformer',
                'machine_name' => 'terraformer',
                'description' => 'The terraformer increases the usable surface of planets.',
                'description_long' => 'With the increasing construction on planets, even the living space for the colony is becoming more and more limited. Traditional methods such as high-rise and underground construction are increasingly becoming insufficient. A small group of high-energy physicists and nano engineers eventually came to the solution: terraforming.
Making use of tremendous amounts of energy, the terraformer can make whole stretches of land or even continents arable. This building houses the production of nanites created specifically for this purpose, which ensure a consistent ground quality throughout.


Each terraformer level allows 5 fields to be cultivated. With each level, the terraformer occupies one field itself. Every 2 terraformer levels you will receive 1 bonus field.

Once built, the terraformer cannot be dismantled.',
                'requirements' => [15 => 1, 113 => 12],
                'price' => [
                    'metal' => 50000,
                    'crystal' => 0,
                    'deuterium' => 100000,
                    'energy' => 1000,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'terraformer_small.jpg',
                        'micro' => 'terraformer_micro.jpg',
                    ],
                ],
            ],
            36 => [
                'id' => 36,
                'type' => 'station',
                'title' => 'Space Dock',
                'machine_name' => 'space_dock',
                'description' => 'Wreckages can be repaired in the Space Dock.',
                'description_long' => 'The Space Dock offers the possibility to repair ships destroyed in battle which left behind wreckage. The repair time takes a maximum of 12 hours, but it takes at least 30 minutes until the ships can be put back into service.

Repairs must begin within 3 days of the creation of the wreckage. The repaired ships must be returned to duty manually after completion of the repairs. If this is not done, individual ships of any type will be returned to service after 3 days.
Wreckage only appears if more than 150,000 units have been destroyed including one’s own ships which took part in the combat with a value of at least 5% of the ship points.

Since the Space Dock floats in orbit, it does not require a planet field.',
                'requirements' => [22 => 2],
                'price' => [
                    'metal' => 200,
                    'crystal' => 0,
                    'deuterium' => 50,
                    'energy' => 50,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'space_dock_small.jpg',
                        'micro' => 'space_dock_micro.jpg',
                    ],
                ],
            ],
        ];

        $this->researchObjects = [
            // Research
            113 => [
                'id' => 113,
                'type' => 'research',
                'title' => 'Energy Technology',
                'machine_name' => 'energy_technology',
                'description' => 'The command of different types of energy is necessary for many new technologies.',
                'description_long' => 'As various fields of research advanced, it was discovered that the current technology of energy distribution was not sufficient enough to begin certain specialized research. With each upgrade of your Energy Technology, new research can be conducted which unlocks development of more sophisticated ships and defenses.',
                'requirements' => [31 => 1],
                'price' => [
                    'metal' => 0,
                    'crystal' => 800,
                    'deuterium' => 400,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'energy_technology_small.jpg',
                        'micro' => 'energy_technology_micro.jpg',
                    ],
                ],
            ],
            120 => [
                'id' => 120,
                'type' => 'research',
                'title' => 'Laser Technology',
                'machine_name' => 'laser_technology',
                'description' => 'Focusing light produces a beam that causes damage when it strikes an object.',
                'description_long' => 'Lasers (light amplification by stimulated emission of radiation) produce an intense, energy rich emission of coherent light. These devices can be used in all sorts of areas, from optical computers to heavy laser weapons, which effortlessly cut through armour technology. The laser technology provides an important basis for research of other weapon technologies.',
                'requirements' => [113 => 2],
                'price' => [
                    'metal' => 200,
                    'crystal' => 100,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            121 => [
                'id' => 121,
                'type' => 'research',
                'title' => 'Ion Technology',
                'machine_name' => 'ion_technology',
                'description' => 'The concentration of ions allows for the construction of cannons, which can inflict enormous damage and reduce the deconstruction costs per level by 4%.',
                'description_long' => 'Ions can be concentrated and accelerated into a deadly beam. These beams can then inflict enormous damage. Our scientists have also developed a technique that will clearly reduce the deconstruction costs for buildings and systems. For each research level, the deconstruction costs will sink by 4%.',
                'requirements' => [113 => 4, 31 => 4, 120 => 5],
                'price' => [
                    'metal' => 1000,
                    'crystal' => 300,
                    'deuterium' => 100,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            114 => [
                'id' => 114,
                'type' => 'research',
                'title' => 'Hyperspace Technology',
                'machine_name' => 'hyperspace_technology',
                'description' => 'By integrating the 4th and 5th dimensions it is now possible to research a new kind of drive that is more economical and efficient.',
                'description_long' => 'In theory, the idea of hyperspace travel relies on the existence of a separate and adjacent dimension. When activated, a hyperspace drive shunts the starship into this other dimension, where it can cover vast distances in an amount of time greatly reduced from the time it would take in "normal" space. Once it reaches the point in hyperspace that corresponds to its destination in real space, it re-emerges.
Once a sufficient level of Hyperspace Technology is researched, the Hyperspace Drive is no longer just a theory. Each improvement to this drive increases the load capacity of your ships by 5% of the base value.',
                'requirements' => [113 => 5, 31 => 7, 110 => 5],
                'price' => [
                    'metal' => 4000,
                    'crystal' => 0,
                    'deuterium' => 2000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            122 => [
                'id' => 122,
                'type' => 'research',
                'title' => 'Plasma Technology',
                'machine_name' => 'plasma_technology',
                'description' => 'A further development of ion technology which accelerates high-energy plasma, which then inflicts devastating damage and additionally optimises the production of metal, crystal and deuterium (1%/0.66%/0.33% per level).',
                'description_long' => 'A further development of ion technology that doesn`t speed up ions but high-energy plasma instead, which can then inflict devastating damage on impact with an object. Our scientists have also found a way to noticeably improve the mining of metal and crystal using this technology.

Metal production increases by 1%, crystal production by 0.66% and deuterium production by 0.33% per construction level of the plasma technology.',
                'requirements' => [113 => 4, 31 => 4, 120 => 5],
                'price' => [
                    'metal' => 2000,
                    'crystal' => 4000,
                    'deuterium' => 1000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            115 => [
                'id' => 115,
                'type' => 'research',
                'title' => 'Combustion Drive',
                'machine_name' => 'combustion_drive',
                'description' => 'The development of this drive makes some ships faster, although each level increases speed by only 10 % of the base value.',
                'description_long' => 'The Combustion Drive is the oldest of technologies, but is still in use. With the Combustion Drive, exhaust is formed from propellants carried within the ship prior to use. In a closed chamber, the pressures are equal in each direction and no acceleration occurs. If an opening is provided at the bottom of the chamber then the pressure is no longer opposed on that side. The remaining pressure gives a resultant thrust in the side opposite the opening, which propels the ship forward by expelling the exhaust rearwards at extreme high speed.

With each level of the Combustion Drive developed, the speed of small and large cargo ships, light fighters, recyclers, and espionage probes are increased by 10%.',
                'requirements' => [113 => 1],
                'price' => [
                    'metal' => 400,
                    'crystal' => 0,
                    'deuterium' => 600,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'combustion_drive_small.jpg',
                        'micro' => 'combustion_drive_micro.jpg',
                    ],
                ],
            ],
            117 => [
                'id' => 117,
                'type' => 'research',
                'title' => 'Impulse Drive',
                'machine_name' => 'impulse_drive',
                'description' => 'The impulse drive is based on the reaction principle. Further development of this drive makes some ships faster, although each level increases speed by only 20 % of the base value.',
                'description_long' => 'The impulse drive is based on the recoil principle, by which the stimulated emission of radiation is mainly produced as a waste product from the core fusion to gain energy. Additionally, other masses can be injected. With each level of the Impulse Drive developed, the speed of bombers, cruisers, heavy fighters, and colony ships are increased by 20% of the base value. Additionally, the small transporters are fitted with impulse drives as soon as their research level reaches 5. As soon as Impulse Drive research has reached level 17, Recyclers are refitted with Impulse Drives.

Interplanetary missiles also travel farther with each level.',
                'requirements' => [113 => 1, 31 => 2],
                'price' => [
                    'metal' => 2000,
                    'crystal' => 4000,
                    'deuterium' => 600,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'impulse_drive_small.jpg',
                        'micro' => 'impulse_drive_micro.jpg',
                    ],
                ],
            ],
            118 => [
                'id' => 118,
                'type' => 'research',
                'title' => 'Hyperspace Drive',
                'machine_name' => 'hyperspace_drive',
                'description' => 'Hyperspace drive warps space around a ship. The development of this drive makes some ships faster, although each level increases speed by only 30 % of the base value.',
                'description_long' => 'In the immediate vicinity of the ship, the space is warped so that long distances can be covered very quickly. The more the Hyperspace Drive is developed, the stronger the warped nature of the space, whereby the speed of the ships equipped with it (Battlecruisers, Battleships, Destroyers, Deathstars, Pathfinders and Reapers) increase by 30% per level. Additionally, the bomber is built with a Hyperspace Drive as soon as research reaches level 8. As soon as Hyperspace Drive research reaches level 15, the Recycler is refitted with a Hyperspace Drive.',
                'requirements' => [114 => 3],
                'price' => [
                    'metal' => 10000,
                    'crystal' => 20000,
                    'deuterium' => 6000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'hyperspace_drive_micro.jpg',
                        'micro' => 'hyperspace_drive_micro.jpg',
                    ],
                ],
            ],
            106 => [
                'id' => 106,
                'type' => 'research',
                'title' => 'Espionage Technology',
                'machine_name' => 'espionage_technology',
                'description' => 'Information about other planets and moons can be gained using this technology.',
                'description_long' => 'Espionage Technology is, in the first instance, an advancement of sensor technology. The more advanced this technology is, the more information the user receives about activities in his environment.
The differences between your own spy level and opposing spy levels is crucial for probes. The more advanced your own espionage technology is, the more information the report can gather and the smaller the chance is that your espionage activities are discovered. The more probes that you send on one mission, the more details they can gather from the target planet. But at the same time it also increases the chance of discovery.
Espionage technology also improves the chance of locating foreign fleets. The espionage level is vital in determining this. From level 2 onwards, the exact total number of attacking ships is displayed as well as the normal attack notification. And from level 4 onwards, the type of attacking ships as well as the total number is shown and from level 8 onwards the exact number of different ship types is shown.
This technology is indispensable for an upcoming attack, as it informs you whether the victim fleet has defense available or not. That is why this technology should be researched very early on.',
                'requirements' => [31 => 3],
                'price' => [
                    'metal' => 200,
                    'crystal' => 1000,
                    'deuterium' => 200,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'espionage_technology_small.jpg',
                        'micro' => 'espionage_technology_micro.jpg',
                    ],
                ],
            ],
            108 => [
                'id' => 108,
                'type' => 'research',
                'title' => 'Computer Technology',
                'machine_name' => 'computer_technology',
                'description' => 'More fleets can be commanded by increasing computer capacities. Each level of computer technology increases the maximum number of fleets by one.',
                'description_long' => 'Once launched on any mission, fleets are controlled primarily by a series of computers located on the originating planet. These massive computers calculate the exact time of arrival, controls course corrections as needed, calculates trajectories, and regulates flight speeds.

With each level researched, the flight computer is upgraded to allow an additional slot to be launched. Computer technology should be continuously developed throughout the building of your empire.',
                'requirements' => [31 => 1],
                'price' => [
                    'metal' => 0,
                    'crystal' => 400,
                    'deuterium' => 600,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'computer_technology_small.jpg',
                        'micro' => 'computer_technology_micro.jpg',
                    ],
                ],
            ],
            124 => [
                'id' => 124,
                'type' => 'research',
                'title' => 'Astrophysics',
                'machine_name' => 'astrophysics',
                'description' => 'With an astrophysics research module, ships can undertake long expeditions. Every second level of this technology will allow you to colonise an extra planet.',
                'description_long' => 'Further findings in the field of astrophysics allow for the construction of laboratories that can be fitted on more and more ships. This makes long expeditions far into unexplored areas of space possible. In addition these advancements can be used to further colonise the universe. For every two levels of this technology an additional planet can be made usable.
Positions 3 and 13 can be populated from level 4 onwards.
Positions 2 and 14 can be populated from level 6 onwards.
Positions 1 and 15 can be populated from level 8 onwards.',
                'requirements' => [106 => 4, 117 => 3],
                'price' => [
                    'metal' => 4000,
                    'crystal' => 8000,
                    'deuterium' => 4000,
                    'energy' => 0,
                    'factor' => 1.75,
                    'round_nearest_100' => true,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'astrophysics_technology_micro.jpg',
                        'micro' => 'astrophysics_technology_micro.jpg',
                    ],
                ],
            ],
            123 => [
                'id' => 123,
                'type' => 'research',
                'title' => 'Intergalactic Research Network',
                'machine_name' => 'intergalactic_research_network',
                'description' => 'Researchers on different planets communicate via this network.',
                'description_long' => 'This is your deep space network to communicate research results to your colonies. With the IRN, faster research times can be achieved by linking the highest level research labs equal to the level of the IRN developed.

In order to function, each colony must be able to conduct the research independently.',
                'requirements' => [108 => 8, 117 => 8, 31 => 10],
                'price' => [
                    'metal' => 240000,
                    'crystal' => 400000,
                    'deuterium' => 160000,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            199 => [
                'id' => 199,
                'type' => 'research',
                'title' => 'Graviton Technology',
                'machine_name' => 'graviton_technology',
                'description' => 'Firing a concentrated charge of graviton particles can create an artificial gravity field, which can destroy ships or even moons.',
                'description_long' => 'A graviton is an elementary particle that is massless and has no cargo. It determines the gravitational power. By firing a concentrated load of gravitons, an artificial gravitational field can be constructed. Not unlike a black hole, it draws mass into itself. Thus it can destroy ships and even entire moons. To produce a sufficient amount of gravitons, huge amounts of energy are required. Graviton Research is required to construct a destructive Deathstar.',
                'requirements' => [31 => 12],
                'price' => [
                    'metal' => 0,
                    'crystal' => 0,
                    'deuterium' => 0,
                    'energy' => 300000,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'robot_factory_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            109 => [
                'id' => 109,
                'type' => 'research',
                'title' => 'Weapon Technology',
                'machine_name' => 'weapon_technology',
                'description' => 'Weapons technology makes weapons systems more efficient. Each level of weapons technology increases the weapon strength of units by 10 % of the base value.',
                'description_long' => 'Weapons Technology is a key research technology and is critical to your survival against enemy Empires. With each level of Weapons Technology researched, the weapons systems on ships and your defense mechanisms become increasingly more efficient. Each level increases the base strength of your weapons by 10% of the base value.',
                'requirements' => [31 => 12],
                'price' => [
                    'metal' => 0,
                    'crystal' => 800,
                    'deuterium' => 200,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'weapons_technology_small.jpg',
                        'micro' => 'weapons_technology_micro.jpg',
                    ],
                ],
            ],
            110 => [
                'id' => 110,
                'type' => 'research',
                'title' => 'Shielding Technology',
                'machine_name' => 'shielding_technology',
                'description' => 'Shielding technology makes the shields on ships and defensive facilities more efficient. Each level of shield technology increases the strength of the shields by 10 % of the base value.',
                'description_long' => 'With the invention of the magnetosphere generator, scientists learned that an artificial shield could be produced to protect the crew in space ships not only from the harsh solar radiation environment in deep space, but also provide protection from enemy fire during an attack. Once scientists finally perfected the technology, a magnetosphere generator was installed on all ships and defense systems.

As the technology is advanced to each level, the magnetosphere generator is upgraded which provides an additional 10% strength to the shields base value.',
                'requirements' => [31 => 6, 113 => 3],
                'price' => [
                    'metal' => 200,
                    'crystal' => 600,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'shielding_technology_small.jpg',
                        'micro' => 'shielding_technology_micro.jpg',
                    ],
                ],
            ],
            111 => [
                'id' => 111,
                'type' => 'research',
                'title' => 'Armour Technology',
                'machine_name' => 'armor_technology',
                'description' => 'Special alloys improve the armour on ships and defensive structures. The effectiveness of the armour can be increased by 10 % per level.',
                'description_long' => 'The environment of deep space is harsh. Pilots and crew on various missions not only faced intense solar radiation, they also faced the prospect of being hit by space debris, or destroyed by enemy fire in an attack. With the discovery of an aluminum-lithium titanium carbide alloy, which was found to be both light weight and durable, this afforded the crew a certain degree of protection. With each level of Armour Technology developed, a higher quality alloy is produced, which increases the armours strength by 10%.',
                'requirements' => [31 => 2],
                'price' => [
                    'metal' => 1000,
                    'crystal' => 0,
                    'deuterium' => 0,
                    'energy' => 0,
                    'factor' => 2,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'armor_technology_small.jpg',
                        'micro' => 'armor_technology_micro.jpg',
                    ],
                ],
            ],
        ];

        $this->militaryShipObjects = [
            // Military ships
            204 => [
                'id' => 204,
                'type' => 'ship',
                'title' => 'Light Fighter',
                'machine_name' => 'light_fighter',
                'class_name' => 'fighterLight',
                'description' => 'This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defenses.',
                'description_long' => 'This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable when it is on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defenses.',
                'requirements' => [21 => 1, 115 => 1],
                'price' => [
                    'metal' => 3000,
                    'crystal' => 1000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 4000,
                    'shield' => 10,
                    'attack' => 50,
                    'speed' => 12500,
                    'capacity' => 50,
                    'fuel' => 20,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'light_fighter_small.jpg',
                        'micro' => 'light_fighter_small.jpg',
                    ],
                ],
            ],
            205 => [
                'id' => 205,
                'type' => 'ship',
                'title' => 'Heavy Fighter',
                'machine_name' => 'heavy_fighter',
                'class_name' => 'fighterHeavy',
                'description' => 'This fighter is better armoured and has a higher attack strength than the light fighter.',
                'description_long' => 'In developing the heavy fighter, researchers reached a point at which conventional drives no longer provided sufficient performance. In order to move the ship optimally, the impulse drive was used for the first time. This increased the costs, but also opened new possibilities. By using this drive, there was more energy left for weapons and shields; in addition, high-quality materials were used for this new family of fighters. With these changes, the heavy fighter represents a new era in ship technology and is the basis for cruiser technology.

Slightly larger than the light fighter, the heavy fighter has thicker hulls, providing more protection, and stronger weaponry.',
                'requirements' => [21 => 3, 111 => 2, 117 => 2],
                'price' => [
                    'metal' => 6000,
                    'crystal' => 4000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 10000,
                    'shield' => 25,
                    'attack' => 150,
                    'speed' => 10000,
                    'capacity' => 100,
                    'fuel' => 75,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'heavy_fighter_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            206 => [
                'id' => 206,
                'type' => 'ship',
                'title' => 'Cruiser',
                'machine_name' => 'cruiser',
                'class_name' => 'cruiser',
                'description' => 'Cruisers are armoured almost three times as heavily as heavy fighters and have more than twice the firepower. In addition, they are very fast.',
                'description_long' => 'With the development of the heavy laser and the ion cannon, light and heavy fighters encountered an alarmingly high number of defeats that increased with each raid. Despite many modifications, weapons strength and armour changes, it could not be increased fast enough to effectively counter these new defensive measures. Therefore, it was decided to build a new class of ship that combined more armor and more firepower. As a result of years of research and development, the Cruiser was born.

Cruisers are armored almost three times of that of the heavy fighters, and possess more than twice the firepower of any combat ship in existence. They also possess speeds that far surpassed any spacecraft ever made. For almost a century, cruisers dominated the universe. However, with the development of Gauss cannons and plasma turrets, their predominance ended. They are still used today against fighter groups, but not as predominantly as before.',
                'requirements' => [21 => 5, 117 => 4, 121 => 2],
                'price' => [
                    'metal' => 20000,
                    'crystal' => 7000,
                    'deuterium' => 2000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 27000,
                    'shield' => 50,
                    'attack' => 400,
                    'speed' => 15000,
                    'capacity' => 800,
                    'fuel' => 300,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'cruiser_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            207 => [
                'id' => 207,
                'type' => 'ship',
                'title' => 'Battleship',
                'machine_name' => 'battle_ship',
                'class_name' => 'battleship',
                'description' => 'Battleships form the backbone of a fleet. Their heavy cannons, high speed, and large cargo holds make them opponents to be taken seriously.',
                'description_long' => 'Once it became apparent that the cruiser was losing ground to the increasing number of defense structures it was facing, and with the loss of ships on missions at unacceptable levels, it was decided to build a ship that could face those same type of defense structures with as little loss as possible. After extensive development, the Battleship was born. Built to withstand the largest of battles, the Battleship features large cargo spaces, heavy cannons, and high hyperdrive speed. Once developed, it eventually turned out to be the backbone of every raiding Emperors fleet.',
                'requirements' => [21 => 7, 118 => 4],
                'price' => [
                    'metal' => 45000,
                    'crystal' => 15000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 60000,
                    'shield' => 200,
                    'attack' => 1000,
                    'speed' => 10000,
                    'capacity' => 1500,
                    'fuel' => 500,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'battleship_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            215 => [
                'id' => 215,
                'type' => 'ship',
                'title' => 'Battlecruiser',
                'machine_name' => 'battlecruiser',
                'class_name' => 'interceptor',
                'description' => 'The Battlecruiser is highly specialized in the interception of hostile fleets.',
                'description_long' => 'This ship is one of the most advanced fighting ships ever to be developed, and is particularly deadly when it comes to destroying attacking fleets. With its improved laser cannons on board and advanced Hyperspace engine, the Battlecruiser is a serious force to be dealt with in any attack. Due to the ships design and its large weapons system, the cargo holds had to be cut, but this is compensated for by the lowered fuel consumption.',
                'requirements' => [21 => 8, 118 => 5, 120 => 12],
                'price' => [
                    'metal' => 30000,
                    'crystal' => 40000,
                    'deuterium' => 15000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 70000,
                    'shield' => 400,
                    'attack' => 700,
                    'speed' => 10000,
                    'capacity' => 750,
                    'fuel' => 250,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'battlecruiser_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            211 => [
                'id' => 211,
                'type' => 'ship',
                'title' => 'Bomber',
                'machine_name' => 'bomber',
                'class_name' => 'bomber',
                'description' => 'The bomber was developed especially to destroy the planetary defenses of a world.',
                'description_long' => 'Over the centuries, as defenses were starting to get larger and more sophisticated, fleets were starting to be destroyed at an alarming rate. It was decided that a new ship was needed to break defenses to ensure maximum results. After years of research and development, the Bomber was created.

Using laser-guided targeting equipment and Plasma Bombs, the Bomber seeks out and destroys any defense mechanism it can find. As soon as the hyperspace drive is developed to Level 8, the Bomber is retrofitted with the hyperspace engine and can fly at higher speeds.',
                'requirements' => [21 => 8, 117 => 6, 122 => 5],
                'price' => [
                    'metal' => 50000,
                    'crystal' => 25000,
                    'deuterium' => 15000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 75000,
                    'shield' => 500,
                    'attack' => 1000,
                    'speed' => 4000,
                    'capacity' => 500,
                    'fuel' => 700,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'bomber_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            213 => [
                'id' => 213,
                'type' => 'ship',
                'title' => 'Destroyer',
                'machine_name' => 'destroyer',
                'class_name' => 'destroyer',
                'description' => 'The destroyer is the king of the warships.',
                'description_long' => 'The Destroyer is the result of years of work and development. With the development of Deathstars, it was decided that a class of ship was needed to defend against such a massive weapon. Thanks to its improved homing sensors, multi-phalanx Ion cannons, Gauss Cannons and Plasma Turrets, the Destroyer
turned out to be one of the most fearsome ships created.

Because the destroyer is very large, its manoeuvrability is severely limited, which makes it more of a battle station than a fighting ship. The lack of manoeuvrability is made up for by its sheer firepower, but it also costs significant amounts of deuterium to build and operate.',
                'requirements' => [21 => 9, 118 => 6, 114 => 5],
                'price' => [
                    'metal' => 60000,
                    'crystal' => 50000,
                    'deuterium' => 15000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 110000,
                    'shield' => 500,
                    'attack' => 200,
                    'speed' => 5000,
                    'capacity' => 2000,
                    'fuel' => 1000,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'destroyer_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            214 => [
                'id' => 214,
                'type' => 'ship',
                'title' => 'Deathstar',
                'machine_name' => 'deathstar',
                'class_name' => 'deathstar',
                'description' => 'The destructive power of the deathstar is unsurpassed.',
                'description_long' => 'The Deathstar is the most powerful ship ever created. This moon sized ship is the only ship that can be seen with the naked eye on the ground. By the time you spot it, unfortunately, it is too late to do anything.

Armed with a gigantic graviton cannon, the most advanced weapons system ever created in the Universe, this massive ship has not only the capability of destroying entire fleets and defenses, but also has the capability of destroying entire moons. Only the most advanced empires have the capability to build a ship of this mammoth size.',
                'requirements' => [21 => 12, 199 => 1, 118 => 7, 114 => 6],
                'price' => [
                    'metal' => 5000000,
                    'crystal' => 4000000,
                    'deuterium' => 1000000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 9000000,
                    'shield' => 50000,
                    'attack' => 200000,
                    'speed' => 100,
                    'capacity' => 1000000,
                    'fuel' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'deathstar_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
        ];

        $this->civilShipObjects = [
            // Civil ships
            202 => [
                'id' => 202,
                'type' => 'ship',
                'title' => 'Small Cargo',
                'machine_name' => 'small_cargo',
                'class_name' => 'transporterSmall',
                'description' => 'The small cargo is an agile ship which can quickly transport resources to other planets.',
                'description_long' => 'Transporters are about as large as fighters, yet they forego high-performance drives and on-board weaponry for gains in their freighting capacity. As a result, a transporter should only be sent into battles when it is accompanied by combat-ready ships.

As soon as the Impulse Drive reaches research level 5, the small transporter travels with increased base speed and is geared with an Impulse Drive.',
                'requirements' => [21 => 2, 115 => 2],
                'price' => [
                    'metal' => 2000,
                    'crystal' => 2000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 4000,
                    'shield' => 10,
                    'attack' => 5,
                    'speed' => 10000,
                    'capacity' => 6250,
                    'fuel' => 10,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'small_cargo_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            203 => [
                'id' => 203,
                'type' => 'ship',
                'title' => 'Large Cargo',
                'machine_name' => 'large_cargo',
                'class_name' => 'transporterLarge',
                'description' => 'This cargo ship has a much larger cargo capacity than the small cargo, and is generally faster thanks to an improved drive.',
                'description_long' => 'As time evolved, the raids on colonies resulted in larger and larger amounts of resources being captured. As a result, Small Cargos were being sent out in mass numbers to compensate for the larger captures. It was quickly learned that a new class of ship was needed to maximize resources captured in raids, yet also be cost
effective. After much development, the Large Cargo was born.

To maximize the resources that can be stored in the holds, this ship has little in the way of weapons or armor. Thanks to the highly developed combustion engine installed, it serves as the most economical resource supplier between planets, and most effective in raids on hostile worlds.',
                'requirements' => [21 => 4, 115 => 6],
                'price' => [
                    'metal' => 6000,
                    'crystal' => 6000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 12000,
                    'shield' => 25,
                    'attack' => 5,
                    'speed' => 15000,
                    'capacity' => 31250,
                    'fuel' => 50,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'large_cargo_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            208 => [
                'id' => 208,
                'type' => 'ship',
                'title' => 'Colony Ship',
                'machine_name' => 'colony_ship',
                'class_name' => 'colonyShip',
                'description' => 'Vacant planets can be colonised with this ship.',
                'description_long' => 'In the 20th Century, Man decided to go for the stars. First, it was landing on the Moon. After that, a space station was built. Mars was colonized soon afterwards. It was soon determined that our growth depended on colonizing other worlds. Scientists and engineers all over the world gathered together to develop mans greatest achievement ever. The Colony Ship is born.

This ship is used to prepare a newly discovered planet for colonization. Once it arrives at the destination, the ship is instantly transformed into habitual living space to assist in populating and mining the new world. The maximum number of planets is thereby determined by the progress in astrophysics research.Two new levels of Astrotechnology allow for the colonization of one additional planet.',
                'requirements' => [21 => 4, 117 => 3],
                'price' => [
                    'metal' => 10000,
                    'crystal' => 20000,
                    'deuterium' => 10000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 30000,
                    'shield' => 100,
                    'attack' => 50,
                    'speed' => 2500,
                    'capacity' => 7500,
                    'fuel' => 1000,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'colony_ship_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            209 => [
                'id' => 209,
                'type' => 'ship',
                'title' => 'Recycler',
                'machine_name' => 'recycler',
                'class_name' => 'recycler',
                'description' => 'Recyclers are the only ships able to harvest debris fields floating in a planet`s orbit after combat.',
                'description_long' => 'Combat in space took on ever larger scales. Thousands of ships were destroyed and the resources of their remains seemed to be lost to the debris fields forever. Normal cargo ships couldn`t get close enough to these fields without risking substantial damage.
A recent development in shield technologies efficiently bypassed this issue. A new class of ships were created that were similar to the Transporters: the Recyclers. Their efforts helped to gather the thought-lost resources and then salvage them. The debris no longer posed any real danger thanks to the new shields.

As soon as Impulse Drive research has reached level 17, Recyclers are refitted with Impulse Drives. As soon as Hyperspace Drive research has reached level 15, Recyclers are refitted with Hyperspace Drives.',
                'requirements' => [21 => 4, 115 => 6, 110 => 2],
                'price' => [
                    'metal' => 10000,
                    'crystal' => 6000,
                    'deuterium' => 2000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 16000,
                    'shield' => 10,
                    'attack' => 1,
                    'speed' => 2000,
                    'capacity' => 20000,
                    'fuel' => 300,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'recycler_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            210 => [
                'id' => 210,
                'type' => 'ship',
                'title' => 'Espionage Probe',
                'machine_name' => 'espionage_probe',
                'class_name' => 'espionageProbe',
                'description' => 'Espionage probes are small, agile drones that provide data on fleets and planets over great distances.',
                'description_long' => 'Espionage probes are small, agile drones that provide data on fleets and planets. Fitted with specially designed engines, it allows them to cover vast distances in only a few minutes. Once in orbit around the target planet, they quickly collect data and transmit the report back via your Deep Space Network for evaluation. But there is a risk to the intelligent gathering aspect. During the time the report is transmitted back to your network, the signal can be detected by the target and the probes can be destroyed.',
                'requirements' => [21 => 3, 115 => 3, 106 => 2],
                'price' => [
                    'metal' => 0,
                    'crystal' => 1000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 1000,
                    'shield' => 0,
                    'attack' => 0,
                    'speed' => 100000000,
                    'capacity' => 0,
                    'fuel' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'espionage_probe_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            212 => [
                'id' => 212,
                'type' => 'ship',
                'title' => 'Solar Satellite',
                'machine_name' => 'solar_satellite',
                'description' => 'Solar satellites are simple platforms of solar cells, located in a high, stationary orbit. They gather sunlight and transmit it to the ground station via laser. A solar satellite produces 25 energy on this planet.',
                'description_long' => 'Scientists discovered a method of transmitting electrical energy to the colony using specially designed satellites in a geosynchronous orbit. Solar Satellites gather solar energy and transmit it to a ground station using advanced laser technology. The efficiency of a solar satellite depends on the strength of the solar radiation it receives. In principle, energy production in orbits closer to the sun is greater than for planets in orbits distant from the sun.
Due to their good cost/performance ratio solar satellites can solve a lot of energy problems. But beware: Solar satellites can be easily destroyed in battle.',
                'requirements' => [21 => 1],
                'price' => [
                    'metal' => 0,
                    'crystal' => 2000,
                    'deuterium' => 500,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 2000,
                    'shield' => 1,
                    'attack' => 1,
                    'speed' => 0,
                    'capacity' => 0,
                    'fuel' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'solar_satellite_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
        ];

        $this->defenseObjects = [
            // Defense
            401 => [
                'id' => 401,
                'type' => 'defense',
                'title' => 'Rocket Launcher',
                'machine_name' => 'rocket_launcher',
                'description' => 'The rocket launcher is a simple, cost-effective defensive option.',
                'description_long' => 'Your first basic line of defense. These are simple ground based launch facilities that fire conventional warhead tipped missiles at attacking enemy targets. As they are cheap to construct and no research is required, they are well suited for defending raids, but lose effectiveness defending from larger scale attacks. Once you begin construction on more advanced defense weapons systems, Rocket Launchers become simple fodder to allow your more damaging weapons to inflict greater damage for a longer period of time.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 1],
                'price' => [
                    'metal' => 2000,
                    'crystal' => 0,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 2000,
                    'shield' => 20,
                    'attack' => 80,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'rocket_launcher_small.jpg',
                        'micro' => 'rocket_launcher_small.jpg',
                    ],
                ],
            ],
            402 => [
                'id' => 402,
                'type' => 'defense',
                'title' => 'Light Laser',
                'machine_name' => 'light_laser',
                'description' => 'Concentrated firing at a target with photons can produce significantly greater damage than standard ballistic weapons.',
                'description_long' => 'As technology developed and more sophisticated ships were created, it was determined that a stronger line of defense was needed to counter the attacks. As Laser Technology advanced, a new weapon was designed to provide the next level of defense. Light Lasers are simple ground based weapons that utilize special targeting systems to track the enemy and fire a high intensity laser designed to cut through the hull of the target. In order to be kept cost effective, they were fitted with an improved shielding system, however the structural integrity is the same as that of the Rocket Launcher.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 2, 120 => 3],
                'price' => [
                    'metal' => 1500,
                    'crystal' => 500,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 2000,
                    'shield' => 25,
                    'attack' => 100,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'light_laser_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            403 => [
                'id' => 403,
                'type' => 'defense',
                'title' => 'Heavy Laser',
                'machine_name' => 'heavy_laser',
                'description' => 'The heavy laser is the logical development of the light laser.',
                'description_long' => 'The Heavy Laser is a practical, improved version of the Light Laser. Being more balanced than the Light Laser with improved alloy composition, it utilizes stronger, more densely packed beams, and even better onboard targeting systems.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 4, 120 => 6, 113 => 3],
                'price' => [
                    'metal' => 6000,
                    'crystal' => 2000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 8000,
                    'shield' => 100,
                    'attack' => 250,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'heavy_laser_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            404 => [
                'id' => 404,
                'type' => 'defense',
                'title' => 'Gauss Cannon',
                'machine_name' => 'gauss_cannon',
                'description' => 'The Gauss Cannon fires projectiles weighing tons at high speeds.',
                'description_long' => 'For a long time projectile weapons were regarded as antiquated in the wake of modern thermonuclear and energy technology and due to the development of the hyperdrive and improved armour. That was until the exact energy technology that had once aged it, helped it to re-achieve their established position.
A gauss cannon is a large version of the particle accelerator. Extremely heavy missiles are accelerated with a huge electromagnetic force and have muzzle velocities that make the dirt surrounding the missile burn in the skies. This weapon is so powerful when fired that it creates a sonic boom. Modern armour and shields can barely withstand the force, often the target is completely penetrated by the power of the missile. Defense structures deactivate as soon as they have been too badly damaged.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 6, 109 => 3, 113 => 6, 110 => 1],
                'price' => [
                    'metal' => 20000,
                    'crystal' => 15000,
                    'deuterium' => 2000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 35000,
                    'shield' => 200,
                    'attack' => 1100,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'gauss_cannon_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            405 => [
                'id' => 405,
                'type' => 'defense',
                'title' => 'Ion Cannon',
                'machine_name' => 'ion_cannon',
                'description' => 'The Ion Cannon fires a continuous beam of accelerating ions, causing considerable damage to objects it strikes.',
                'description_long' => 'An ion cannon is a weapon that fires beams of ions (positively or negatively charged particles). The Ion Cannon is actually a type of Particle Cannon; only the particles used are ionized. Due to their electrical charges, they also have the potential to disable electronic devices, and anything else that has an electrical or similar power source, using a phenomena known as the the Electromagetic Pulse (EMP effect). Due to the cannons highly improved shielding system, this cannon provides improved protection for your larger, more destructive defense weapons.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 4, 121 => 4],
                'price' => [
                    'metal' => 2000,
                    'crystal' => 6000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 8000,
                    'shield' => 500,
                    'attack' => 150,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'ion_cannon_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            406 => [
                'id' => 406,
                'type' => 'defense',
                'title' => 'Plasma Turret',
                'machine_name' => 'plasma_turret',
                'description' => 'Plasma Turrets release the energy of a solar flare and surpass even the destroyer in destructive effect.',
                'description_long' => 'One of the most advanced defense weapons systems ever developed, the Plasma Turret uses a large nuclear reactor fuel cell to power an electromagnetic accelerator that fires a pulse, or toroid, of plasma. During operation, the Plasma turret first locks on a target and begins the process of firing. A plasma sphere is created in the turrets core by super heating and compressing gases, stripping them of their ions. Once the gas is superheated, compressed, and a plasma sphere is created, it is then loaded into the electromagnetic accelerator which is energized. Once fully energized, the accelerator is activated, which results in the plasma sphere being launched at an extremely high rate of speed to the intended target. From the targets perspective, the approaching bluish ball of plasma is impressive, but once it strikes, it causes instant destruction.

Defensive facilities deactivate as soon as they are too heavily damaged. After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 8, 122 => 7],
                'price' => [
                    'metal' => 50000,
                    'crystal' => 50000,
                    'deuterium' => 30000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 100000,
                    'shield' => 300,
                    'attack' => 3000,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'plasma_turret_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            407 => [
                'id' => 407,
                'type' => 'defense',
                'title' => 'Small Shield Dome',
                'machine_name' => 'small_shield_dome',
                'description' => 'The small shield dome covers an entire planet with a field which can absorb a tremendous amount of energy.',
                'description_long' => 'Colonizing new worlds brought about a new danger, space debris. A large asteroid could easily wipe out the world and all inhabitants. Advancements in shielding technology provided scientists with a way to develop a shield to protect an entire planet not only from space debris but, as it was learned, from an enemy attack. By creating a large electromagnetic field around the planet, space debris that would normally have destroyed the planet was deflected, and attacks from enemy Empires were thwarted. The first generators were large and the shield provided moderate protection, but it was later discovered that small shields did not afford the protection from larger scale attacks. The small shield dome was the prelude to a stronger, more advanced planetary shielding system to come.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 1, 110 => 2],
                'price' => [
                    'metal' => 10000,
                    'crystal' => 10000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 20000,
                    'shield' => 2000,
                    'attack' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'small_shield_dome_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            408 => [
                'id' => 408,
                'type' => 'defense',
                'title' => 'Large Shield Dome',
                'machine_name' => 'large_shield_dome',
                'description' => 'The evolution of the small shield dome can employ significantly more energy to withstand attacks.',
                'description_long' => 'The Large Shield Dome is the next step in the advancement of planetary shields, it is the result of years of work improving the Small Shield Dome. Built to withstand a larger barrage of enemy fire by providing a higher energized electromagnetic field, large domes provide a longer period of protection before collapsing.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.',
                'requirements' => [21 => 6, 110 => 6],
                'price' => [
                    'metal' => 50000,
                    'crystal' => 50000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 100000,
                    'shield' => 10000,
                    'attack' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'large_shield_dome_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            502 => [
                'id' => 502,
                'type' => 'defense',
                'title' => 'Anti-Ballistic Missiles',
                'machine_name' => 'anti_ballistic_missile',
                'description' => 'Anti-Ballistic Missiles destroy attacking interplanetary missiles',
                'description_long' => 'Anti Ballistic Missiles (ABM) are your only line of defense when attacked by Interplanetary Missiles (IPM) on your planet or moon. When a launch of IPMs is detected, these missiles automatically arm, process a launch code in their flight computers, target the inbound IPM, and launch to intercept. During the flight, the target IPM is constantly tracked and course corrections are applied until the ABM reaches the target and destroys the attacking IPM. Each ABM destroys one incoming IPM.',
                'requirements' => [44 => 2],
                'price' => [
                    'metal' => 8000,
                    'crystal' => 2000,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 8000,
                    'shield' => 1,
                    'attack' => 1,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'solar_satellite_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
            503 => [
                'id' => 503,
                'type' => 'defense',
                'title' => 'Interplanetary Missiles',
                'machine_name' => 'interplanetary_missile',
                'description' => 'Interplanetary Missiles destroy enemy defenses. Your interplanetary missiles have got a coverage of ?? systems.',
                'description_long' => 'Interplanetary Missiles (IPM) are your offensive weapon to destroy the defenses of your target. Using state of the art tracking technology, each missile targets a certain number of defenses for destruction. Tipped with an anti-matter bomb, they deliver a destructive force so severe that destroyed shields and defenses cannot be repaired. The only way to counter these missiles is with ABMs.',
                'requirements' => [44 => 4, 117 => 1],
                'price' => [
                    'metal' => 12500,
                    'crystal' => 2500,
                    'deuterium' => 10000,
                    'energy' => 0,
                ],
                'properties' => [
                    'structural_integrity' => 15000,
                    'shield' => 1,
                    'attack' => 12000,
                ],
                'assets' => [
                    'img' => [
                        'small' => 'solar_satellite_small.jpg',
                        'micro' => 'robot_factory_micro.jpg',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get all buildings (or specific building).
     */
    public function getBuildingObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->buildingObjects[$object_id])) {
                return $this->buildingObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->buildingObjects;
        }
    }

    /**
     * Get all buildings that have production values.
     */
    public function getBuildingObjectsWithProduction($object_id = FALSE)
    {
        $return = array();

        foreach ($this->buildingObjects as $key => $value) {
            if (!empty(($value['production']))) {
                $return[$key] = $value;
            }
        }

        if (!empty($object_id)) {
            return $return[$object_id];
        }

        return $return;
    }

    /**
     * Get all buildings that have storage values.
     */
    public function getBuildingObjectsWithStorage($object_id = FALSE)
    {
        $return = array();

        foreach ($this->buildingObjects as $key => $value) {
            if (!empty(($value['storage']))) {
                $return[$key] = $value;
            }
        }

        if (!empty($object_id)) {
            return $return[$object_id];
        }

        return $return;
    }

    /**
     * Get all buildings (or specific building).
     */
    public function getStationObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->stationObjects[$object_id])) {
                return $this->stationObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->stationObjects;
        }
    }

    /**
     * Get all research (or specific research).
     */
    public function getResearchObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->researchObjects[$object_id])) {
                return $this->researchObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->researchObjects;
        }
    }

    /**
     * Get all ships (or specific ship).
     */
    public function getShipObjects($object_id = FALSE)
    {
        $ship_objects = $this->militaryShipObjects + $this->civilShipObjects;

        if (!empty($object_id)) {
            if (!empty($ship_objects[$object_id])) {
                return $ship_objects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $ship_objects;
        }
    }

    /**
     * Get all military ships (or specific ship).
     */
    public function getMilitaryShipObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->militaryShipObjects[$object_id])) {
                return $this->militaryShipObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->militaryShipObjects;
        }
    }

    /**
     * Get all civil ships (or specific ship).
     */
    public function getCivilShipObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->civilShipObjects[$object_id])) {
                return $this->civilShipObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->civilShipObjects;
        }
    }

    /**
     * Get all defense (or specific defense).
     */
    public function getDefenseObjects($object_id = FALSE)
    {
        if (!empty($object_id)) {
            if (!empty($this->defenseObjects[$object_id])) {
                return $this->defenseObjects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $this->defenseObjects;
        }
    }

    /**
     * Check if object requirements are met (for building it).
     *
     * @param $building_id
     *
     * @return bool
     */
    public function objectRequirementsMet($building_id, PlanetService $planet, PlayerService $player)
    {
        $objects = $this->getObjects();
        $requirements = $objects[$building_id]['requirements'];

        foreach ($requirements as $requirement_id => $requirement_level) {
            $object = $objects[$requirement_id];
            if ($object['type'] == 'research') {
                if ($player->getResearchLevel($requirement_id) < $requirement_level) {
                    return FALSE;
                }
            } else {
                if ($planet->getObjectLevel($requirement_id) < $requirement_level) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    /**
     * Get all objects (or specific object).
     */
    public function getObjects($object_id = FALSE)
    {
        // Create combined array of all object types.
        $all_objects = $this->buildingObjects +
            $this->stationObjects +
            $this->researchObjects +
            $this->militaryShipObjects +
            $this->civilShipObjects +
            $this->defenseObjects;

        if (!empty($object_id)) {
            if (!empty($all_objects[$object_id])) {
                return $all_objects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $all_objects;
        }
    }

    /**
     * Get all unit objects (or specific unit object).
     */
    public function getUnitObjects($object_id = FALSE)
    {
        // Create combined array of the required object types.
        $unit_objects = $this->militaryShipObjects + $this->civilShipObjects + $this->defenseObjects;

        if (!empty($object_id)) {
            if (!empty($unit_objects[$object_id])) {
                return $unit_objects[$object_id];
            } else {
                return FALSE;
            }
        } else {
            return $unit_objects;
        }
    }

    /**
     * Calculates the max build amount of an object (unit) based on available
     * planet resources.
     */
    public function getObjectMaxBuildAmount($object_id, PlanetService $planet)
    {
        $price = $this->getObjectPrice($object_id, $planet);

        // Calculate max build amount based on price
        $max_build_amount = [];
        if ($price['metal'] > 0) {
            $max_build_amount[] = floor($planet->getMetal() / $price['metal']);
        }

        if ($price['crystal'] > 0) {
            $max_build_amount[] = floor($planet->getCrystal() / $price['crystal']);
        }

        if ($price['deuterium'] > 0) {
            $max_build_amount[] = floor($planet->getDeuterium() / $price['deuterium']);
        }

        if ($price['energy'] > 0) {
            $max_build_amount[] = floor($planet->getEnergy() / $price['energy']);
        }

        // Get lowest divided value which is the maximum amount of times this ship
        // can be built right now.
        $max_build_amount = min($max_build_amount);

        return $max_build_amount;
    }

    /**
     * Gets the cost of upgrading a building on this planet to the next level.
     */
    public function getObjectPrice($object_id, PlanetService $planet, $formatted = FALSE)
    {
        $object = $this->getObjects($object_id);
        $player = $planet->getPlayer();

        // Price calculation for buildings or research (price depends on level)
        if ($object['type'] == 'building' || $object['type'] == 'research') {
            if ($object['type'] == 'building') {
                $current_level = $planet->getObjectLevel($object['id']);
            } else {
                $current_level = $player->getResearchLevel($object['id']);
            }

            $price = $this->getObjectRawPrice($object_id, $current_level + 1);
        }
        // Price calculation for fleet or defense (regular price per unit)
        else {
            $price = $this->getObjectRawPrice($object_id);
        }

        // Optionally format the output.
        if ($formatted) {
            foreach ($price as &$element) {
                $element = AppUtil::formatNumber($element);
            }
        }

        return $price;
    }

    /**
     * Gets the cost of building a building of a certain level or a unit.
     */
    public function getObjectRawPrice($object_id, $level = NULL)
    {
        $object = $this->getObjects($object_id);

        // Price calculation for buildings or research (price depends on level)
        if ($object['type'] == 'building' || $object['type'] == 'research') {
            // Level 0 is free.
            if ($level == 0) {
                return [
                    'metal' => 0,
                    'crystal' => 0,
                    'deuterium' => 0,
                    'energy' => 0,
                ];
            }

            $base_price = $object['price'];

            // Calculate price.
            $price = [];
            $price['metal'] = $base_price['metal'] * pow($base_price['factor'], $level - 1);
            $price['crystal'] = $base_price['crystal'] * pow($base_price['factor'], $level - 1);
            $price['deuterium'] = $base_price['deuterium'] * pow($base_price['factor'], $level - 1);
            $price['energy'] = $base_price['energy'] * pow($base_price['factor'], $level - 1);

            // Round price
            $price['metal'] = round($price['metal']);
            $price['crystal'] = round($price['crystal']);
            $price['deuterium'] = round($price['deuterium']);
            $price['energy'] = round($price['energy']);

            if (!empty($base_price['round_nearest_100'])) {
                // Round to nearest 100.
                $price['metal'] = round($price['metal'] / 100) * 100;
                $price['crystal'] = round($price['crystal'] / 100) * 100;
                $price['deuterium'] = round($price['deuterium'] / 100) * 100;
                $price['energy'] = round($price['energy']);
            }
        }
        // Price calculation for fleet or defense (regular price per unit)
        else {
            $price = $object['price'];
        }

        return $price;
    }
}

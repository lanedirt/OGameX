<?php

namespace OGame\Services;

use Illuminate\Support\Facades\DB;

/**
 * Class ObjectService.
 *
 * Contains all information about game objects such as buildings, research,
 * ships, defense etc.
 *
 * @package OGame\Services
 */
class ObjectService {

  /**
   * Buildings property.
   */
  protected $buildings;

  /**
   * Planet constructor.
   */
  public function __construct() {
    $this->buildings = [
      // Resources -- buildings
      1 => [
        'id' => 1,
        'type' => 'building',
        'title' => 'Metal Mine',
        'machine_name' => 'metal_mine',
        'description' => 'Used in the extraction of metal ore, metal mines are of primary importance to all emerging and established empires.',
        'description_long' => 'Metal is the primary resource used in the foundation of your Empire. At greater depths, the mines can produce more output of viable metal for use in the construction of buildings, ships, defence systems, and research. As the mines drill deeper, more energy is required for maximum production. As metal is the most abundant of all resources available, its value is considered to be the lowest of all resources for trading.',
        'requirements' => [],
        'price' => [
          'metal' => 60,
          'crystal' => 15,
          'deuterium' => 0,
          'energy' => 0,
          'factor' => 1.5,
        ],
        'production' => [
          'metal' => 'return   (30 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
          'crystal' => 'return   "0";',
          'deuterium' => 'return   "0";',
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
          'metal' => 'return   "0";',
          'crystal' => 'return   (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
          'deuterium' => 'return   "0";',
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
          'metal' => 'return   "0";',
          'crystal' => 'return   "0";',
          'deuterium' => 'return  ((10 * $building_level * pow((1.1), $building_level)) * (-0.002 * $planet_temperature + 1.28))  * (0.1 * $building_percentage);',
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
          'metal' => 'return   "0";',
          'crystal' => 'return   "0";',
          'deuterium' => 'return  "0";',
          'energy' => 'return   (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
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
          'metal' => 'return   "0";',
          'crystal' => 'return   "0";',
          'deuterium' => 'return  -  (10 * $building_level * pow(1.1, $building_level));',
          'energy' => 'return   (30 * $building_level * pow((1.05 + $energy_technology_level * 0.01), $building_level)) * (0.1 * $building_percentage);',
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
      // Facilities
      14 => [
        'id' => 14,
        'type' => 'building',
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
        'type' => 'building',
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
        'type' => 'building',
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
        'type' => 'building',
        'title' => 'Alliance Depot',
        'machine_name' => 'alliance_depot',
        'description' => 'The alliance depot supplies fuel to friendly fleets in orbit helping with defence.',
        'description_long' => 'The alliance depot supplies fuel to friendly fleets in orbit helping with defence. For each upgrade level of the alliance depot, a special demand of deuterium per hour can be sent to an orbiting fleet.',
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
        'type' => 'building',
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
        'type' => 'building',
        'title' => 'Nanite Factory',
        'machine_name' => 'nanite_factory',
        'description' => 'This is the ultimate in robotics technology. Each level cuts the construction time for buildings, ships, and defences.',
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
        'type' => 'building',
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
        'type' => 'building',
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
      // Research
      113 => [
        'id' => 113,
        'type' => 'research',
        'title' => 'Energy Technology',
        'machine_name' => 'energy_technology',
        'description' => 'The command of different types of energy is necessary for many new technologies.',
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
        'requirements' => [106 => 4, 117 => 3],
        'price' => [
          'metal' => 4000,
          'crystal' => 8000,
          'deuterium' => 4000,
          'energy' => 0,
          'factor' => 2,
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
      // Ships
      204 => [
        'id' => 204,
        'type' => 'ship',
        'title' => 'Light Fighter',
        'machine_name' => 'light_fighter',
        'description' => 'This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defences.',
        'requirements' => [21 => 1, 115 => 1],
        'price' => [
          'metal' => 3000,
          'crystal' => 1000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'This fighter is better armoured and has a higher attack strength than the light fighter.',
        'requirements' => [21 => 3, 111 => 2, 117 => 2],
        'price' => [
          'metal' => 6000,
          'crystal' => 4000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'Cruisers are armoured almost three times as heavily as heavy fighters and have more than twice the firepower. In addition, they are very fast.',
        'requirements' => [21 => 5, 117 => 4, 121 => 2],
        'price' => [
          'metal' => 20000,
          'crystal' => 7000,
          'deuterium' => 2000,
          'energy' => 0,
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
        'machine_name' => 'battleship',
        'description' => 'Battleships form the backbone of a fleet. Their heavy cannons, high speed, and large cargo holds make them opponents to be taken seriously.',
        'requirements' => [21 => 7, 118 => 4],
        'price' => [
          'metal' => 45000,
          'crystal' => 15000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'The Battlecruiser is highly specialized in the interception of hostile fleets.',
        'requirements' => [21 => 8, 118 => 5, 120 => 12],
        'price' => [
          'metal' => 30000,
          'crystal' => 40000,
          'deuterium' => 15000,
          'energy' => 0,
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
        'description' => 'The bomber was developed especially to destroy the planetary defences of a world.',
        'requirements' => [21 => 8, 117 => 6, 122 => 5],
        'price' => [
          'metal' => 50000,
          'crystal' => 25000,
          'deuterium' => 15000,
          'energy' => 0,
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
        'description' => 'The destroyer is the king of the warships.',
        'requirements' => [21 => 9, 118 => 6, 114 => 5],
        'price' => [
          'metal' => 60000,
          'crystal' => 50000,
          'deuterium' => 15000,
          'energy' => 0,
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
        'description' => 'The destructive power of the deathstar is unsurpassed.',
        'requirements' => [21 => 12, 199 => 1, 118 => 7, 114 => 6],
        'price' => [
          'metal' => 5000000,
          'crystal' => 4000000,
          'deuterium' => 1000000,
          'energy' => 0,
        ],
        'assets' => [
          'img' => [
            'small' => 'deathstar_small.jpg',
            'micro' => 'robot_factory_micro.jpg',
          ],
        ],
      ],
      202 => [
        'id' => 202,
        'type' => 'ship',
        'title' => 'Small Cargo',
        'machine_name' => 'small_cargo',
        'description' => 'The small cargo is an agile ship which can quickly transport resources to other planets.',
        'requirements' => [21 => 2, 115 => 2],
        'price' => [
          'metal' => 2000,
          'crystal' => 2000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'This cargo ship has a much larger cargo capacity than the small cargo, and is generally faster thanks to an improved drive.',
        'requirements' => [21 => 4, 115 => 6],
        'price' => [
          'metal' => 6000,
          'crystal' => 6000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'Vacant planets can be colonised with this ship.',
        'requirements' => [21 => 4, 117 => 3],
        'price' => [
          'metal' => 10000,
          'crystal' => 20000,
          'deuterium' => 10000,
          'energy' => 0,
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
        'description' => 'Recyclers are the only ships able to harvest debris fields floating in a planet`s orbit after combat.',
        'requirements' => [21 => 4, 115 => 6, 110 => 2],
        'price' => [
          'metal' => 10000,
          'crystal' => 6000,
          'deuterium' => 2000,
          'energy' => 0,
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
        'description' => 'Espionage probes are small, agile drones that provide data on fleets and planets over great distances.',
        'requirements' => [21 => 3, 115 => 3, 106 => 2],
        'price' => [
          'metal' => 0,
          'crystal' => 1000,
          'deuterium' => 0,
          'energy' => 0,
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
        'assets' => [
          'img' => [
            'small' => 'solar_satellite_small.jpg',
            'micro' => 'robot_factory_micro.jpg',
          ],
        ],
      ],
      // Defense
      401 => [
        'id' => 401,
        'type' => 'defense',
        'title' => 'Rocket Launcher',
        'machine_name' => 'rocket_launcher',
        'description' => 'The rocket launcher is a simple, cost-effective defensive option.',
        'requirements' => [21 => 1],
        'price' => [
          'metal' => 2000,
          'crystal' => 0,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [21 => 2, 120 => 3],
        'price' => [
          'metal' => 1500,
          'crystal' => 500,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [21 => 4, 120 => 6, 113 => 3],
        'price' => [
          'metal' => 6000,
          'crystal' => 2000,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [21 => 6, 109 => 3, 113 => 6, 110 => 1],
        'price' => [
          'metal' => 20000,
          'crystal' => 15000,
          'deuterium' => 2000,
          'energy' => 0,
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
        'requirements' => [21 => 4, 121 => 4],
        'price' => [
          'metal' => 2000,
          'crystal' => 6000,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [21 => 8, 122 => 7],
        'price' => [
          'metal' => 50000,
          'crystal' => 50000,
          'deuterium' => 30000,
          'energy' => 0,
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
        'requirements' => [21 => 1, 110 => 2],
        'price' => [
          'metal' => 10000,
          'crystal' => 10000,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [21 => 6, 110 => 6],
        'price' => [
          'metal' => 50000,
          'crystal' => 50000,
          'deuterium' => 0,
          'energy' => 0,
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
        'requirements' => [44 => 2],
        'price' => [
          'metal' => 8000,
          'crystal' => 2000,
          'deuterium' => 0,
          'energy' => 0,
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
        'description' => 'Interplanetary Missiles destroy enemy defences. Your interplanetary missiles have got a coverage of ?? systems.',
        'requirements' => [44 => 4, 117 => 1],
        'price' => [
          'metal' => 12500,
          'crystal' => 2500,
          'deuterium' => 10000,
          'energy' => 0,
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
   * Get buildings.
   */
  public function getBuildings($object_id = FALSE) {
    if (!empty($object_id)) {
      if (!empty($this->buildings[$object_id])) {
        return $this->buildings[$object_id];
      }
      else {
        return FALSE;
      }
    }
    else {
      return $this->buildings;
    }
  }

  /**
   * Get all buildings that have production values.
   */
  public function getBuildingsWithProduction($object_id = FALSE) {
    $return = array();

    foreach ($this->buildings as $key => $value) {
      if (!empty(($value['production']))) {
        $return[$key] = $value;
      }
    }

    return $return;
  }

  /**
   * Get all buildings that have storage values.
   */
  public function getBuildingsWithStorage($object_id = FALSE) {
    $return = array();

    foreach ($this->buildings as $key => $value) {
      if (!empty(($value['storage']))) {
        $return[$key] = $value;
      }
    }

    return $return;
  }

  /**
   * Check if object requirements are met (for building it).
   *
   * @param $building_id
   *
   * @return bool
   */
  public function objectRequirementsMet($building_id, PlanetService $planet, PlayerService $player) {
    $buildings = $this->getBuildings();
    $requirements = $buildings[$building_id]['requirements'];

    foreach ($requirements as $requirement_id => $requirement_level) {
      $building = $buildings[$requirement_id];
      // @TODO: refactor into object get level
      if ($building['type'] == 'building') {
        if ($planet->getBuildingLevel($requirement_id) < $requirement_level) {
          return FALSE;
        }
      }
      elseif ($building['type'] == 'research') {
        if ($player->getResearchLevel($requirement_id) < $requirement_level) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  /**
   * Gets the cost of upgrading a building on this planet to the next level.
   */
  public function getObjectPrice($object_id, PlanetService $planet, $formatted = FALSE) {
    $building = $this->getBuildings($object_id);

    // Sanity check: if building does not exist yet then return empty array.
    // @TODO: remove when all buildings have been included.
    if (empty($building)) {
      return [];
    }

    $player = $planet->getPlayer();

    // Price calculation for buildings or research (price depends on level)
    if ($building['type'] == 'building' || $building['type'] == 'research') {
      // @TODO: refactor into object get level
      if ($building['type'] == 'building') {
        $current_level = $planet->getBuildingLevel($building['id']);
      } else {
        $current_level = $player->getResearchLevel($building['id']);
      }

      $base_price = $building['price'];

      // Calculate price.
      $price = [];
      $price['metal'] = $base_price['metal'] * pow($base_price['factor'], $current_level);
      $price['crystal'] = $base_price['crystal'] * pow($base_price['factor'], $current_level);
      $price['deuterium'] = $base_price['deuterium'] * pow($base_price['factor'], $current_level);
      $price['energy'] = $base_price['energy'] * pow($base_price['factor'], $current_level);

      // Round prices down.
      $price['metal'] = floor($price['metal']);
      $price['crystal'] = floor($price['crystal']);
      $price['deuterium'] = floor($price['deuterium']);
      $price['energy'] = floor($price['energy']);
    }
    // Price calculation for fleet or defense (regular price per unit)
    else {
      $price = $building['price'];
    }

    // Optionally format the output.
    if ($formatted) {
      foreach ($price as &$element) {
        $element = number_format($element, 0, ',', '.');
      }
    }

    return $price;
  }

  /**
   * Calculates the max build amount of an object (unit) based on available
   * planet resources.
   */
  public function getObjectMaxBuildAmount($object_id, PlanetService $planet) {
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
}

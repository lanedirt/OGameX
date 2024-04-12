<?php

namespace OGame\Services\Objects;

use OGame\Services\Objects\Models\Fields\GameObjectAssets;
use OGame\Services\Objects\Models\Fields\GameObjectPrice;
use OGame\Services\Objects\Models\ResearchObject;
use OGame\Services\Objects\Models\ShipObject;

class ResearchObjects
{
    /**
     * Returns all defined research objects.
     *
     * @return array<ResearchObject>
     */
    public static function get() : array
    {
        $buildingObjectsNew = [];

        // --- Energy Technology ---
        $energyTechnology = new ResearchObject();
        $energyTechnology->id = 113;
        $energyTechnology->title = 'Energy Technology';
        $energyTechnology->machine_name = 'energy_technology';
        $energyTechnology->description = 'The command of different types of energy is necessary for many new technologies.';
        $energyTechnology->description_long = 'As various fields of research advanced, it was discovered that the current technology of energy distribution was not sufficient enough to begin certain specialized research. With each upgrade of your Energy Technology, new research can be conducted which unlocks development of more sophisticated ships and defenses.';
        $energyTechnology->requirements = ['research_lab' => 1];
        $energyTechnology->price = new GameObjectPrice(0, 800, 400, 0, 2);
        $energyTechnology->assets = new GameObjectAssets();
        $energyTechnology->assets->imgMicro = 'energy_technology_micro.jpg';
        $energyTechnology->assets->imgSmall = 'energy_technology_small.jpg';

        $buildingObjectsNew[] = $energyTechnology;





        /*$this->researchObjects = [
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
        ];*/


        return $buildingObjectsNew;
    }
}
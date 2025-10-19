<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\Calculations\CalculationType;
use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\ResearchObject;

class ResearchObjects
{
    /**
     * Returns all research objects.
     *
     * @return array<ResearchObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Energy Technology ---
        $energyTechnology = new ResearchObject();
        $energyTechnology->id = 113;
        $energyTechnology->title = 'Energy Technology';
        $energyTechnology->machine_name = 'energy_technology';
        $energyTechnology->class_name = 'energyTechnology';
        $energyTechnology->description = 'The command of different types of energy is necessary for many new technologies.';
        $energyTechnology->description_long = 'As various fields of research advanced, it was discovered that the current technology of energy distribution was not sufficient enough to begin certain specialized research. With each upgrade of your Energy Technology, new research can be conducted which unlocks development of more sophisticated ships and defenses.';
        $energyTechnology->requirements = [
            new GameObjectRequirement('research_lab', 1),
        ];
        $energyTechnology->price = new GameObjectPrice(0, 800, 400, 0, 2);
        $energyTechnology->assets = new GameObjectAssets();
        $energyTechnology->assets->imgMicro = 'energy_technology_micro.jpg';
        $energyTechnology->assets->imgSmall = 'energy_technology_small.jpg';

        $buildingObjectsNew[] = $energyTechnology;

        // --- Laser Technology ---
        $laserTechnology = new ResearchObject();
        $laserTechnology->id = 120;
        $laserTechnology->title = 'Laser Technology';
        $laserTechnology->machine_name = 'laser_technology';
        $laserTechnology->class_name = 'laserTechnology';
        $laserTechnology->description = 'Focusing light produces a beam that causes damage when it strikes an object.';
        $laserTechnology->description_long = 'Lasers (light amplification by stimulated emission of radiation) produce an intense, energy rich emission of coherent light. These devices can be used in all sorts of areas, from optical computers to heavy laser weapons, which effortlessly cut through armour technology. The laser technology provides an important basis for research of other weapon technologies.';
        $laserTechnology->requirements = [
            new GameObjectRequirement('energy_technology', 2),
            new GameObjectRequirement('research_lab', 1),
        ];
        $laserTechnology->price = new GameObjectPrice(200, 100, 0, 0, 2);
        $laserTechnology->assets = new GameObjectAssets();
        $laserTechnology->assets->imgMicro = 'laser_technology_micro.jpg';
        $laserTechnology->assets->imgSmall = 'laser_technology_small.jpg';

        $buildingObjectsNew[] = $laserTechnology;

        // --- Ion Technology ---
        $ionTechnology = new ResearchObject();
        $ionTechnology->id = 121;
        $ionTechnology->title = 'Ion Technology';
        $ionTechnology->machine_name = 'ion_technology';
        $ionTechnology->class_name = 'ionTechnology';
        $ionTechnology->description = 'The concentration of ions allows for the construction of cannons, which can inflict enormous damage and reduce the deconstruction costs per level by 4%.';
        $ionTechnology->description_long = 'Ions can be concentrated and accelerated into a deadly beam. These beams can then inflict enormous damage. Our scientists have also developed a technique that will clearly reduce the deconstruction costs for buildings and systems. For each research level, the deconstruction costs will sink by 4%.';
        $ionTechnology->requirements = [
            new GameObjectRequirement('research_lab', 4),
            new GameObjectRequirement('energy_technology', 4),
            new GameObjectRequirement('laser_technology', 5),
        ];
        $ionTechnology->price = new GameObjectPrice(1000, 300, 100, 0, 2);
        $ionTechnology->assets = new GameObjectAssets();
        $ionTechnology->assets->imgMicro = 'ion_technology_micro.jpg';
        $ionTechnology->assets->imgSmall = 'ion_technology_small.jpg';

        $buildingObjectsNew[] = $ionTechnology;

        // --- Hyperspace Technology ---
        $hyperspaceTechnology = new ResearchObject();
        $hyperspaceTechnology->id = 114;
        $hyperspaceTechnology->title = 'Hyperspace Technology';
        $hyperspaceTechnology->machine_name = 'hyperspace_technology';
        $hyperspaceTechnology->class_name = 'hyperspaceTechnology';
        $hyperspaceTechnology->description = 'By integrating the 4th and 5th dimensions it is now possible to research a new kind of drive that is more economical and efficient.';
        $hyperspaceTechnology->description_long = 'In theory, the idea of hyperspace travel relies on the existence of a separate and adjacent dimension. When activated, a hyperspace drive shunts the starship into this other dimension, where it can cover vast distances in an amount of time greatly reduced from the time it would take in "normal" space. Once it reaches the point in hyperspace that corresponds to its destination in real space, it re-emerges.
Once a sufficient level of Hyperspace Technology is researched, the Hyperspace Drive is no longer just a theory. Each improvement to this drive increases the load capacity of your ships by 5% of the base value.';
        $hyperspaceTechnology->requirements = [
            new GameObjectRequirement('research_lab', 7),
            new GameObjectRequirement('energy_technology', 5),
            new GameObjectRequirement('shielding_technology', 5),
        ];
        $hyperspaceTechnology->price = new GameObjectPrice(0, 4000, 2000, 0, 2);
        $hyperspaceTechnology->assets = new GameObjectAssets();
        $hyperspaceTechnology->assets->imgMicro = 'hyperspace_technology_micro.jpg';
        $hyperspaceTechnology->assets->imgSmall = 'hyperspace_technology_small.jpg';

        $buildingObjectsNew[] = $hyperspaceTechnology;

        // --- Plasma Technology ---
        $plasmaTechnology = new ResearchObject();
        $plasmaTechnology->id = 122;
        $plasmaTechnology->title = 'Plasma Technology';
        $plasmaTechnology->machine_name = 'plasma_technology';
        $plasmaTechnology->class_name = 'plasmaTechnology';
        $plasmaTechnology->description = 'A further development of ion technology which accelerates high-energy plasma, which then inflicts devastating damage and additionally optimises the production of metal, crystal and deuterium (1%/0.66%/0.33% per level).';
        $plasmaTechnology->description_long = 'A further development of ion technology that doesn`t speed up ions but high-energy plasma instead, which can then inflict devastating damage on impact with an object. Our scientists have also found a way to noticeably improve the mining of metal and crystal using this technology.

Metal production increases by 1%, crystal production by 0.66% and deuterium production by 0.33% per construction level of the plasma technology.';
        $plasmaTechnology->requirements = [
            new GameObjectRequirement('research_lab', 4),
            new GameObjectRequirement('energy_technology', 8),
            new GameObjectRequirement('laser_technology', 10),
            new GameObjectRequirement('ion_technology', 5),
        ];
        $plasmaTechnology->price = new GameObjectPrice(2000, 4000, 1000, 0, 2);
        $plasmaTechnology->assets = new GameObjectAssets();
        $plasmaTechnology->assets->imgMicro = 'plasma_technology_micro.jpg';
        $plasmaTechnology->assets->imgSmall = 'plasma_technology_small.jpg';

        $buildingObjectsNew[] = $plasmaTechnology;

        // --- Combustion Drive ---
        $combustionDrive = new ResearchObject();
        $combustionDrive->id = 115;
        $combustionDrive->title = 'Combustion Drive';
        $combustionDrive->machine_name = 'combustion_drive';
        $combustionDrive->class_name = 'combustionDriveTechnology';
        $combustionDrive->description = 'The development of this drive makes some ships faster, although each level increases speed by only 10 % of the base value.';
        $combustionDrive->description_long = 'The Combustion Drive is the oldest of technologies, but is still in use. With the Combustion Drive, exhaust is formed from propellants carried within the ship prior to use. In a closed chamber, the pressures are equal in each direction and no acceleration occurs. If an opening is provided at the bottom of the chamber then the pressure is no longer opposed on that side. The remaining pressure gives a resultant thrust in the side opposite the opening, which propels the ship forward by expelling the exhaust rearwards at extreme high speed.

With each level of the Combustion Drive developed, the speed of small and large cargo ships, light fighters, recyclers, and espionage probes are increased by 10%.';
        $combustionDrive->requirements = [
            new GameObjectRequirement('energy_technology', 1),
            new GameObjectRequirement('research_lab', 1),
        ];
        $combustionDrive->price = new GameObjectPrice(400, 0, 600, 0, 2);
        $combustionDrive->assets = new GameObjectAssets();
        $combustionDrive->assets->imgMicro = 'combustion_drive_micro.jpg';
        $combustionDrive->assets->imgSmall = 'combustion_drive_small.jpg';

        $buildingObjectsNew[] = $combustionDrive;

        // --- Impulse Drive ---
        $impulseDrive = new ResearchObject();
        $impulseDrive->id = 117;
        $impulseDrive->title = 'Impulse Drive';
        $impulseDrive->machine_name = 'impulse_drive';
        $impulseDrive->class_name = 'impulseDriveTechnology';
        $impulseDrive->description = 'The impulse drive is based on the reaction principle. Further development of this drive makes some ships faster, although each level increases speed by only 20 % of the base value.';
        $impulseDrive->description_long = 'The impulse drive is based on the recoil principle, by which the stimulated emission of radiation is mainly produced as a waste product from the core fusion to gain energy. Additionally, other masses can be injected. With each level of the Impulse Drive developed, the speed of bombers, cruisers, heavy fighters, and colony ships are increased by 20% of the base value. Additionally, the small transporters are fitted with impulse drives as soon as their research level reaches 5. As soon as Impulse Drive research has reached level 17, Recyclers are refitted with Impulse Drives.

        Interplanetary missiles also travel farther with each level.';

        $impulseDrive->requirements = [
            new GameObjectRequirement('energy_technology', 1),
            new GameObjectRequirement('research_lab', 2),
        ];
        $impulseDrive->price = new GameObjectPrice(2000, 4000, 600, 0, 2);
        $impulseDrive->assets = new GameObjectAssets();
        $impulseDrive->assets->imgMicro = 'impulse_drive_micro.jpg';
        $impulseDrive->assets->imgSmall = 'impulse_drive_small.jpg';

        $buildingObjectsNew[] = $impulseDrive;

        // --- Hyperspace Drive ---
        $hyperspaceDrive = new ResearchObject();
        $hyperspaceDrive->id = 118;
        $hyperspaceDrive->title = 'Hyperspace Drive';
        $hyperspaceDrive->machine_name = 'hyperspace_drive';
        $hyperspaceDrive->class_name = 'hyperspaceDriveTechnology';
        $hyperspaceDrive->description = 'Hyperspace drive warps space around a ship. The development of this drive makes some ships faster, although each level increases speed by only 30 % of the base value.';
        $hyperspaceDrive->description_long = 'In the immediate vicinity of the ship, the space is warped so that long distances can be covered very quickly. The more the Hyperspace Drive is developed, the stronger the warped nature of the space, whereby the speed of the ships equipped with it (Battlecruisers, Battleships, Destroyers, Deathstars, Pathfinders and Reapers) increase by 30% per level. Additionally, the bomber is built with a Hyperspace Drive as soon as research reaches level 8. As soon as Hyperspace Drive research reaches level 15, the Recycler is refitted with a Hyperspace Drive.';
        $hyperspaceDrive->requirements = [
            new GameObjectRequirement('research_lab', 7),
            new GameObjectRequirement('hyperspace_technology', 3),
        ];
        $hyperspaceDrive->price = new GameObjectPrice(10000, 20000, 6000, 0, 2);
        $hyperspaceDrive->assets = new GameObjectAssets();
        $hyperspaceDrive->assets->imgMicro = 'hyperspace_drive_micro.jpg';
        $hyperspaceDrive->assets->imgSmall = 'hyperspace_drive_small.jpg';

        $buildingObjectsNew[] = $hyperspaceDrive;

        // --- Espionage Technology ---
        $espionageTechnology = new ResearchObject();
        $espionageTechnology->id = 106;
        $espionageTechnology->title = 'Espionage Technology';
        $espionageTechnology->machine_name = 'espionage_technology';
        $espionageTechnology->class_name = 'espionageTechnology';
        $espionageTechnology->description = 'Information about other planets and moons can be gained using this technology.';
        $espionageTechnology->description_long = 'Espionage Technology is, in the first instance, an advancement of sensor technology. The more advanced this technology is, the more information the user receives about activities in his environment.
        The differences between your own spy level and opposing spy levels is crucial for probes. The more advanced your own espionage technology is, the more information the report can gather and the smaller the chance is that your espionage activities are discovered. The more probes that you send on one mission, the more details they can gather from the target planet. But at the same time it also increases the chance of discovery.
        Espionage technology also improves the chance of locating foreign fleets. The espionage level is vital in determining this. From level 2 onwards, the exact total number of attacking ships is displayed as well as the normal attack notification. And from level 4 onwards, the type of attacking ships as well as the total number is shown and from level 8 onwards the exact number of different ship types is shown.
        This technology is indispensable for an upcoming attack, as it informs you whether the victim fleet has defense available or not. That is why this technology should be researched very early on.';
        $espionageTechnology->requirements = [
            new GameObjectRequirement('research_lab', 3),
        ];
        $espionageTechnology->price = new GameObjectPrice(200, 1000, 200, 0, 2);
        $espionageTechnology->assets = new GameObjectAssets();
        $espionageTechnology->assets->imgMicro = 'espionage_technology_micro.jpg';
        $espionageTechnology->assets->imgSmall = 'espionage_technology_small.jpg';

        $buildingObjectsNew[] = $espionageTechnology;

        // --- Computer Technology ---
        $computerTechnology = new ResearchObject();
        $computerTechnology->id = 108;
        $computerTechnology->title = 'Computer Technology';
        $computerTechnology->machine_name = 'computer_technology';
        $computerTechnology->class_name = 'computerTechnology';
        $computerTechnology->description = 'More fleets can be commanded by increasing computer capacities. Each level of computer technology increases the maximum number of fleets by one.';
        $computerTechnology->description_long = 'Once launched on any mission, fleets are controlled primarily by a series of computers located on the originating planet. These massive computers calculate the exact time of arrival, controls course corrections as needed, calculates trajectories, and regulates flight speeds.
        With each level researched, the flight computer is upgraded to allow an additional slot to be launched. Computer technology should be continuously developed throughout the building of your empire.';
        $computerTechnology->requirements = [
            new GameObjectRequirement('research_lab', 1),
        ];
        $computerTechnology->price = new GameObjectPrice(0, 400, 600, 0, 2);
        $computerTechnology->assets = new GameObjectAssets();
        $computerTechnology->assets->imgMicro = 'computer_technology_micro.jpg';
        $computerTechnology->assets->imgSmall = 'computer_technology_small.jpg';

        // Add custom calculation formulas for max fleet slots.
        $computerTechnology->addCalculation(CalculationType::MAX_FLEET_SLOTS, function (int $level) {
            // Starts with 1, and every level of computer research adds 1 more slot.
            return 1 + $level;
        });

        $buildingObjectsNew[] = $computerTechnology;

        // --- Astrophysics ---
        $astrophysics = new ResearchObject();
        $astrophysics->id = 124;
        $astrophysics->title = 'Astrophysics';
        $astrophysics->machine_name = 'astrophysics';
        $astrophysics->class_name = 'astrophysicsTechnology';
        $astrophysics->description = 'With an astrophysics research module, ships can undertake long expeditions. Every second level of this technology will allow you to colonise an extra planet.';
        $astrophysics->description_long = 'Further findings in the field of astrophysics allow for the construction of laboratories that can be fitted on more and more ships. This makes long expeditions far into unexplored areas of space possible. In addition these advancements can be used to further colonise the universe. For every two levels of this technology an additional planet can be made usable.';
        $astrophysics->requirements = [
            new GameObjectRequirement('impulse_drive', 3),
            new GameObjectRequirement('research_lab', 3),
            new GameObjectRequirement('espionage_technology', 4),
        ];
        $astrophysics->price = new GameObjectPrice(4000, 8000, 4000, 0, 1.75, true);
        $astrophysics->assets = new GameObjectAssets();
        $astrophysics->assets->imgMicro = 'astrophysics_technology_micro.jpg';
        $astrophysics->assets->imgSmall = 'astrophysics_technology_small.jpg';

        // Add custom calculation formulas for max colonies and max expeditions.
        $astrophysics->addCalculation(CalculationType::MAX_COLONIES, function (int $level) {
            return round($level / 2);
        });
        $astrophysics->addCalculation(CalculationType::MAX_EXPEDITION_SLOTS, function (int $level) {
            return floor(sqrt($level));
        });

        $buildingObjectsNew[] = $astrophysics;

        // --- Intergalactic Research Network ---
        $intergalacticResearchNetwork = new ResearchObject();
        $intergalacticResearchNetwork->id = 123;
        $intergalacticResearchNetwork->title = 'Intergalactic Research Network';
        $intergalacticResearchNetwork->machine_name = 'intergalactic_research_network';
        $intergalacticResearchNetwork->class_name = 'researchNetworkTechnology';
        $intergalacticResearchNetwork->description = 'Researchers on different planets communicate via this network.';
        $intergalacticResearchNetwork->description_long = 'This is your deep space network to communicate research results to your colonies. With the IRN, faster research times can be achieved by linking the highest level research labs equal to the level of the IRN developed.
        In order to function, each colony must be able to conduct the research independently.';
        $intergalacticResearchNetwork->requirements = [
            new GameObjectRequirement('computer_technology', 8),
            new GameObjectRequirement('research_lab', 10),
            new GameObjectRequirement('hyperspace_technology', 8),
        ];
        $intergalacticResearchNetwork->price = new GameObjectPrice(240000, 400000, 160000, 0, 2);
        $intergalacticResearchNetwork->assets = new GameObjectAssets();
        $intergalacticResearchNetwork->assets->imgMicro = 'intergalactic_research_network_micro.jpg';
        $intergalacticResearchNetwork->assets->imgSmall = 'intergalactic_research_network_small.jpg';

        $buildingObjectsNew[] = $intergalacticResearchNetwork;

        // --- Graviton Technology ---
        $gravitonTechnology = new ResearchObject();
        $gravitonTechnology->id = 199;
        $gravitonTechnology->title = 'Graviton Technology';
        $gravitonTechnology->machine_name = 'graviton_technology';
        $gravitonTechnology->class_name = 'gravitonTechnology';
        $gravitonTechnology->description = 'Firing a concentrated charge of graviton particles can create an artificial gravity field, which can destroy ships or even moons.';

        $gravitonTechnology->description_long = 'A graviton is an elementary particle that is massless and has no cargo. It determines the gravitational power. By firing a concentrated load of gravitons, an artificial gravitational field can be constructed. Not unlike a black hole, it draws mass into itself. Thus it can destroy ships and even entire moons. To produce a sufficient amount of gravitons, huge amounts of energy are required. Graviton Research is required to construct a destructive Deathstar.';
        $gravitonTechnology->requirements = [
            new GameObjectRequirement('research_lab', 12),
        ];
        $gravitonTechnology->price = new GameObjectPrice(0, 0, 0, 300000, 2);
        $gravitonTechnology->assets = new GameObjectAssets();
        $gravitonTechnology->assets->imgMicro = 'graviton_technology_micro.jpg';
        $gravitonTechnology->assets->imgSmall = 'graviton_technology_small.jpg';

        $buildingObjectsNew[] = $gravitonTechnology;

        // --- Weapon Technology ---
        $weaponTechnology = new ResearchObject();
        $weaponTechnology->id = 109;
        $weaponTechnology->title = 'Weapon Technology';
        $weaponTechnology->machine_name = 'weapon_technology';
        $weaponTechnology->class_name = 'weaponsTechnology';
        $weaponTechnology->description = 'Weapons technology makes weapons systems more efficient. Each level of weapons technology increases the weapon strength of units by 10 % of the base value.';
        $weaponTechnology->description_long = 'Weapons Technology is a key research technology and is critical to your survival against enemy Empires. With each level of Weapons Technology researched, the weapons systems on ships and your defense mechanisms become increasingly more efficient. Each level increases the base strength of your weapons by 10% of the base value.';
        $weaponTechnology->requirements = [
            new GameObjectRequirement('research_lab', 4),
        ];
        $weaponTechnology->price = new GameObjectPrice(800, 200, 0, 0, 2);
        $weaponTechnology->assets = new GameObjectAssets();
        $weaponTechnology->assets->imgMicro = 'weapons_technology_micro.jpg';
        $weaponTechnology->assets->imgSmall = 'weapons_technology_small.jpg';

        $buildingObjectsNew[] = $weaponTechnology;

        // --- Shielding Technology ---
        $shieldingTechnology = new ResearchObject();
        $shieldingTechnology->id = 110;
        $shieldingTechnology->title = 'Shield Technology';
        $shieldingTechnology->machine_name = 'shielding_technology';
        $shieldingTechnology->class_name = 'shieldingTechnology';
        $shieldingTechnology->description = 'Shield technology makes the shields on ships and defensive facilities more efficient. Each level of shield technology increases the strength of the shields by 10 % of the base value.';
        $shieldingTechnology->description_long = 'With the invention of the magnetosphere generator, scientists learned that an artificial shield could be produced to protect the crew in space ships not only from the harsh solar radiation environment in deep space, but also provide protection from enemy fire during an attack. Once scientists finally perfected the technology, a magnetosphere generator was installed on all ships and defense systems.

        As the technology is advanced to each level, the magnetosphere generator is upgraded which provides an additional 10% strength to the shields base value.';
        $shieldingTechnology->requirements = [
            new GameObjectRequirement('research_lab', 6),
            new GameObjectRequirement('energy_technology', 3),
        ];
        $shieldingTechnology->price = new GameObjectPrice(200, 600, 0, 0, 2);
        $shieldingTechnology->assets = new GameObjectAssets();
        $shieldingTechnology->assets->imgMicro = 'shielding_technology_micro.jpg';
        $shieldingTechnology->assets->imgSmall = 'shielding_technology_small.jpg';

        $buildingObjectsNew[] = $shieldingTechnology;

        // --- Armour Technology ---
        $armourTechnology = new ResearchObject();
        $armourTechnology->id = 111;
        $armourTechnology->title = 'Armour Technology';
        $armourTechnology->machine_name = 'armor_technology';
        $armourTechnology->class_name = 'armorTechnology';
        $armourTechnology->description = 'Special alloys improve the armour on ships and defensive structures. The effectiveness of the armour can be increased by 10 % per level.';
        $armourTechnology->description_long = 'The environment of deep space is harsh. Pilots and crew on various missions not only faced intense solar radiation, they also faced the prospect of being hit by space debris, or destroyed by enemy fire in an attack. With the discovery of an aluminum-lithium titanium carbide alloy, which was found to be both light weight and durable, this afforded the crew a certain degree of protection. With each level of Armour Technology developed, a higher quality alloy is produced, which increases the armours strength by 10%.';
        $armourTechnology->requirements = [
            new GameObjectRequirement('research_lab', 2),
        ];
        $armourTechnology->price = new GameObjectPrice(1000, 0, 0, 0, 2);
        $armourTechnology->assets = new GameObjectAssets();
        $armourTechnology->assets->imgMicro = 'armor_technology_micro.jpg';
        $armourTechnology->assets->imgSmall = 'armor_technology_small.jpg';

        $buildingObjectsNew[] = $armourTechnology;

        return $buildingObjectsNew;
    }
}

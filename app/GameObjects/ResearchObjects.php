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
        $energyTechnology->title = __('t_resources.energy_technology.title');
        $energyTechnology->machine_name = 'energy_technology';
        $energyTechnology->class_name = 'energyTechnology';
        $energyTechnology->description = __('t_resources.energy_technology.description');
        $energyTechnology->description_long = __('t_resources.energy_technology.description_long');
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
        $laserTechnology->title = __('t_resources.laser_technology.title');
        $laserTechnology->machine_name = 'laser_technology';
        $laserTechnology->class_name = 'laserTechnology';
        $laserTechnology->description = __('t_resources.laser_technology.description');
        $laserTechnology->description_long = __('t_resources.laser_technology.description_long');
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
        $ionTechnology->title = __('t_resources.ion_technology.title');
        $ionTechnology->machine_name = 'ion_technology';
        $ionTechnology->class_name = 'ionTechnology';
        $ionTechnology->description = __('t_resources.ion_technology.description');
        $ionTechnology->description_long = __('t_resources.ion_technology.description_long');
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
        $hyperspaceTechnology->title = __('t_resources.hyperspace_technology.title');
        $hyperspaceTechnology->machine_name = 'hyperspace_technology';
        $hyperspaceTechnology->class_name = 'hyperspaceTechnology';
        $hyperspaceTechnology->description = __('t_resources.hyperspace_technology.description');
        $hyperspaceTechnology->description_long = __('t_resources.hyperspace_technology.description_long');
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
        $plasmaTechnology->title = __('t_resources.plasma_technology.title');
        $plasmaTechnology->machine_name = 'plasma_technology';
        $plasmaTechnology->class_name = 'plasmaTechnology';
        $plasmaTechnology->description = __('t_resources.plasma_technology.description');
        $plasmaTechnology->description_long = __('t_resources.plasma_technology.description_long');
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
        $combustionDrive->title = __('t_resources.combustion_drive.title');
        $combustionDrive->machine_name = 'combustion_drive';
        $combustionDrive->class_name = 'combustionDriveTechnology';
        $combustionDrive->description = __('t_resources.combustion_drive.description');
        $combustionDrive->description_long = __('t_resources.combustion_drive.description_long');
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
        $impulseDrive->title = __('t_resources.impulse_drive.title');
        $impulseDrive->machine_name = 'impulse_drive';
        $impulseDrive->class_name = 'impulseDriveTechnology';
        $impulseDrive->description = __('t_resources.impulse_drive.description');
        $impulseDrive->description_long = __('t_resources.impulse_drive.description_long');

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
        $hyperspaceDrive->title = __('t_resources.hyperspace_drive.title');
        $hyperspaceDrive->machine_name = 'hyperspace_drive';
        $hyperspaceDrive->class_name = 'hyperspaceDriveTechnology';
        $hyperspaceDrive->description = __('t_resources.hyperspace_drive.description');
        $hyperspaceDrive->description_long = __('t_resources.hyperspace_drive.description_long');
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
        $espionageTechnology->title = __('t_resources.espionage_technology.title');
        $espionageTechnology->machine_name = 'espionage_technology';
        $espionageTechnology->class_name = 'espionageTechnology';
        $espionageTechnology->description = __('t_resources.espionage_technology.description');
        $espionageTechnology->description_long = __('t_resources.espionage_technology.description_long');
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
        $computerTechnology->title = __('t_resources.computer_technology.title');
        $computerTechnology->machine_name = 'computer_technology';
        $computerTechnology->class_name = 'computerTechnology';
        $computerTechnology->description = __('t_resources.computer_technology.description');
        $computerTechnology->description_long = __('t_resources.computer_technology.description_long');
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
        $astrophysics->title = __('t_resources.astrophysics.title');
        $astrophysics->machine_name = 'astrophysics';
        $astrophysics->class_name = 'astrophysicsTechnology';
        $astrophysics->description = __('t_resources.astrophysics.description');
        $astrophysics->description_long = __('t_resources.astrophysics.description_long');
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
        $intergalacticResearchNetwork->title = __('t_resources.intergalactic_research_network.title');
        $intergalacticResearchNetwork->machine_name = 'intergalactic_research_network';
        $intergalacticResearchNetwork->class_name = 'researchNetworkTechnology';
        $intergalacticResearchNetwork->description = __('t_resources.intergalactic_research_network.description');
        $intergalacticResearchNetwork->description_long = __('t_resources.intergalactic_research_network.description_long');
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
        $gravitonTechnology->title = __('t_resources.graviton_technology.title');
        $gravitonTechnology->machine_name = 'graviton_technology';
        $gravitonTechnology->class_name = 'gravitonTechnology';
        $gravitonTechnology->description = __('t_resources.graviton_technology.description');
        $gravitonTechnology->description_long = __('t_resources.graviton_technology.description_long');
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
        $weaponTechnology->title = __('t_resources.weapon_technology.title');
        $weaponTechnology->machine_name = 'weapon_technology';
        $weaponTechnology->class_name = 'weaponsTechnology';
        $weaponTechnology->description = __('t_resources.weapon_technology.description');
        $weaponTechnology->description_long = __('t_resources.weapon_technology.description_long');
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
        $shieldingTechnology->title = __('t_resources.shielding_technology.title');
        $shieldingTechnology->machine_name = 'shielding_technology';
        $shieldingTechnology->class_name = 'shieldingTechnology';
        $shieldingTechnology->description = __('t_resources.shielding_technology.description');
        $shieldingTechnology->description_long = __('t_resources.shielding_technology.description_long');
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
        $armourTechnology->title = __('t_resources.armor_technology.title');
        $armourTechnology->machine_name = 'armor_technology';
        $armourTechnology->class_name = 'armorTechnology';
        $armourTechnology->description = __('t_resources.armor_technology.description');
        $armourTechnology->description_long = __('t_resources.armor_technology.description_long');
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

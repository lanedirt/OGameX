<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\DefenseObject;
use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectProperties;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;

class DefenseObjects
{
    /**
     * Returns all defense objects.
     *
     * @return array<DefenseObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Rocket Launcher ---
        $rocketLauncher = new DefenseObject();
        $rocketLauncher->id = 401;
        $rocketLauncher->title = __('t_resources.rocket_launcher.title');
        $rocketLauncher->machine_name = 'rocket_launcher';
        $rocketLauncher->class_name = 'rocketLauncher';
        $rocketLauncher->description = __('t_resources.rocket_launcher.description');
        $rocketLauncher->description_long = __('t_resources.rocket_launcher.description_long');
        $rocketLauncher->requirements = [
            new GameObjectRequirement('shipyard', 1),
        ];
        $rocketLauncher->price = new GameObjectPrice(2000, 0, 0, 0);
        $rocketLauncher->properties = new GameObjectProperties($rocketLauncher, 2000, 20, 80, 0, 0, 0);
        $rocketLauncher->assets = new GameObjectAssets();
        $rocketLauncher->assets->imgSmall = 'rocket_launcher_small.jpg';
        $rocketLauncher->assets->imgMicro = 'rocket_launcher_micro.jpg';
        $buildingObjectsNew[] = $rocketLauncher;

        // --- Light Laser ---
        $lightLaser = new DefenseObject();
        $lightLaser->id = 402;
        $lightLaser->title = __('t_resources.light_laser.title');
        $lightLaser->machine_name = 'light_laser';
        $lightLaser->class_name = 'laserCannonLight';
        $lightLaser->description = __('t_resources.light_laser.description');
        $lightLaser->description_long = __('t_resources.light_laser.description_long');
        $lightLaser->requirements = [
            new GameObjectRequirement('shipyard', 2),
            new GameObjectRequirement('laser_technology', 3),
        ];
        $lightLaser->price = new GameObjectPrice(1500, 500, 0, 0);
        $lightLaser->properties = new GameObjectProperties($lightLaser, 2000, 25, 100, 0, 0, 0);
        $lightLaser->assets = new GameObjectAssets();
        $lightLaser->assets->imgSmall = 'light_laser_small.jpg';
        $lightLaser->assets->imgMicro = 'light_laser_micro.jpg';

        $buildingObjectsNew[] = $lightLaser;

        // --- Heavy Laser ---
        $heavyLaser = new DefenseObject();
        $heavyLaser->id = 403;
        $heavyLaser->title = __('t_resources.heavy_laser.title');
        $heavyLaser->machine_name = 'heavy_laser';
        $heavyLaser->class_name = 'laserCannonHeavy';
        $heavyLaser->description = __('t_resources.heavy_laser.description');
        $heavyLaser->description_long = __('t_resources.heavy_laser.description_long');
        $heavyLaser->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('laser_technology', 6),
            new GameObjectRequirement('energy_technology', 3),
        ];
        $heavyLaser->price = new GameObjectPrice(6000, 2000, 0, 0);
        $heavyLaser->properties = new GameObjectProperties($heavyLaser, 8000, 100, 250, 0, 0, 0);
        $heavyLaser->assets = new GameObjectAssets();
        $heavyLaser->assets->imgSmall = 'heavy_laser_small.jpg';
        $heavyLaser->assets->imgMicro = 'heavy_laser_micro.jpg';

        $buildingObjectsNew[] = $heavyLaser;

        // --- Gauss Cannon ---
        $gaussCannon = new DefenseObject();
        $gaussCannon->id = 404;
        $gaussCannon->title = __('t_resources.gauss_cannon.title');
        $gaussCannon->machine_name = 'gauss_cannon';
        $gaussCannon->class_name = 'gaussCannon';
        $gaussCannon->description = __('t_resources.gauss_cannon.description');
        $gaussCannon->description_long = __('t_resources.gauss_cannon.description_long');
        $gaussCannon->requirements = [
            new GameObjectRequirement('shipyard', 6),
            new GameObjectRequirement('weapon_technology', 3),
            new GameObjectRequirement('shielding_technology', 1),
            new GameObjectRequirement('energy_technology', 6),
        ];
        $gaussCannon->price = new GameObjectPrice(20000, 15000, 2000, 0);
        $gaussCannon->properties = new GameObjectProperties($gaussCannon, 35000, 200, 1100, 0, 0, 0);
        $gaussCannon->assets = new GameObjectAssets();
        $gaussCannon->assets->imgSmall = 'gauss_cannon_small.jpg';
        $gaussCannon->assets->imgMicro = 'gauss_cannon_micro.jpg';
        $buildingObjectsNew[] = $gaussCannon;

        // --- Ion Cannon ---
        $ionCannon = new DefenseObject();
        $ionCannon->id = 405;
        $ionCannon->title = __('t_resources.ion_cannon.title');
        $ionCannon->machine_name = 'ion_cannon';
        $ionCannon->class_name = 'ionCannon';
        $ionCannon->description = __('t_resources.ion_cannon.description');
        $ionCannon->description_long = __('t_resources.ion_cannon.description_long');
        $ionCannon->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('ion_technology', 4),
        ];

        $ionCannon->price = new GameObjectPrice(2000, 6000, 0, 0);
        $ionCannon->properties = new GameObjectProperties($ionCannon, 8000, 500, 150, 0, 0, 0);
        $ionCannon->assets = new GameObjectAssets();
        $ionCannon->assets->imgSmall = 'ion_cannon_small.jpg';
        $ionCannon->assets->imgMicro = 'ion_cannon_micro.jpg';
        $buildingObjectsNew[] = $ionCannon;

        // --- Plasma Turret ---
        $plasmaTurret = new DefenseObject();
        $plasmaTurret->id = 406;
        $plasmaTurret->title = __('t_resources.plasma_turret.title');
        $plasmaTurret->machine_name = 'plasma_turret';
        $plasmaTurret->class_name = 'plasmaCannon';
        $plasmaTurret->description = __('t_resources.plasma_turret.description');
        $plasmaTurret->description_long = __('t_resources.plasma_turret.description_long');
        $plasmaTurret->requirements = [
            new GameObjectRequirement('shipyard', 8),
            new GameObjectRequirement('plasma_technology', 7),
        ];
        $plasmaTurret->price = new GameObjectPrice(50000, 50000, 30000, 0);
        $plasmaTurret->properties = new GameObjectProperties($plasmaTurret, 100000, 300, 3000, 0, 0, 0);
        $plasmaTurret->assets = new GameObjectAssets();
        $plasmaTurret->assets->imgSmall = 'plasma_turret_small.jpg';
        $plasmaTurret->assets->imgMicro = 'plasma_turret_micro.jpg';
        $buildingObjectsNew[] = $plasmaTurret;

        // --- Small Shield Dome ---
        $smallShieldDome = new DefenseObject();
        $smallShieldDome->id = 407;
        $smallShieldDome->title = __('t_resources.small_shield_dome.title');
        $smallShieldDome->machine_name = 'small_shield_dome';
        $smallShieldDome->class_name = 'shieldDomeSmall';
        $smallShieldDome->description = __('t_resources.small_shield_dome.description');
        $smallShieldDome->description_long = __('t_resources.small_shield_dome.description_long');
        $smallShieldDome->requirements = [
            new GameObjectRequirement('shipyard', 1),
            new GameObjectRequirement('shielding_technology', 2),
        ];
        $smallShieldDome->price = new GameObjectPrice(10000, 10000, 0, 0);
        $smallShieldDome->properties = new GameObjectProperties($smallShieldDome, 20000, 2000, 1, 0, 0, 0);
        $smallShieldDome->assets = new GameObjectAssets();
        $smallShieldDome->assets->imgSmall = 'small_shield_dome_small.jpg';
        $smallShieldDome->assets->imgMicro = 'small_shield_dome_micro.jpg';

        $buildingObjectsNew[] = $smallShieldDome;

        // --- Large Shield Dome ---
        $largeShieldDome = new DefenseObject();
        $largeShieldDome->id = 408;
        $largeShieldDome->title = __('t_resources.large_shield_dome.title');
        $largeShieldDome->machine_name = 'large_shield_dome';
        $largeShieldDome->class_name = 'shieldDomeLarge';
        $largeShieldDome->description = __('t_resources.large_shield_dome.description');
        $largeShieldDome->description_long = __('t_resources.large_shield_dome.description_long');
        $largeShieldDome->requirements = [
            new GameObjectRequirement('shipyard', 6),
            new GameObjectRequirement('shielding_technology', 6),
        ];
        $largeShieldDome->price = new GameObjectPrice(50000, 50000, 0, 0);
        $largeShieldDome->properties = new GameObjectProperties($largeShieldDome, 100000, 10000, 1, 0, 0, 0);
        $largeShieldDome->assets = new GameObjectAssets();
        $largeShieldDome->assets->imgSmall = 'large_shield_dome_small.jpg';
        $largeShieldDome->assets->imgMicro = 'large_shield_dome_micro.jpg';

        $buildingObjectsNew[] = $largeShieldDome;

        // --- Anti-Ballistic Missiles ---
        $antiBallisticMissile = new DefenseObject();
        $antiBallisticMissile->id = 502;
        $antiBallisticMissile->title = __('t_resources.anti_ballistic_missile.title');
        $antiBallisticMissile->machine_name = 'anti_ballistic_missile';
        $antiBallisticMissile->class_name = 'missileInterceptor';
        $antiBallisticMissile->description = __('t_resources.anti_ballistic_missile.description');
        $antiBallisticMissile->description_long = __('t_resources.anti_ballistic_missile.description_long');
        $antiBallisticMissile->requirements = [
            new GameObjectRequirement('missile_silo', 2),
            new GameObjectRequirement('shipyard', 1),
        ];
        $antiBallisticMissile->price = new GameObjectPrice(8000, 2000, 0, 0);
        $antiBallisticMissile->properties = new GameObjectProperties($antiBallisticMissile, 8000, 1, 1, 0, 0, 0);
        $antiBallisticMissile->assets = new GameObjectAssets();
        $antiBallisticMissile->assets->imgSmall = 'anti_ballistic_missile_small.jpg';
        $antiBallisticMissile->assets->imgMicro = 'anti_ballistic_missile_micro.jpg';

        $buildingObjectsNew[] = $antiBallisticMissile;

        // --- Interplanetary Missiles ---
        $interplanetaryMissile = new DefenseObject();
        $interplanetaryMissile->id = 503;
        $interplanetaryMissile->title = __('t_resources.interplanetary_missile.title');
        $interplanetaryMissile->machine_name = 'interplanetary_missile';
        $interplanetaryMissile->class_name = 'missileInterplanetary';
        $interplanetaryMissile->description = __('t_resources.interplanetary_missile.description');
        $interplanetaryMissile->description_long = __('t_resources.interplanetary_missile.description_long');
        $interplanetaryMissile->requirements = [
            new GameObjectRequirement('shipyard', 1),
            new GameObjectRequirement('missile_silo', 4),
            new GameObjectRequirement('impulse_drive', 1),
        ];
        $interplanetaryMissile->price = new GameObjectPrice(12500, 2500, 10000, 0);
        $interplanetaryMissile->properties = new GameObjectProperties($interplanetaryMissile, 15000, 1, 12000, 0, 0, 0);
        $interplanetaryMissile->assets = new GameObjectAssets();
        $interplanetaryMissile->assets->imgSmall = 'interplanetary_missile_small.jpg';
        $interplanetaryMissile->assets->imgMicro = 'interplanetary_missile_micro.jpg';

        $buildingObjectsNew[] = $interplanetaryMissile;

        return $buildingObjectsNew;
    }
}

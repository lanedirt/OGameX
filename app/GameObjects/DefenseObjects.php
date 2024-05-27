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
        $rocketLauncher->title = 'Rocket Launcher';
        $rocketLauncher->machine_name = 'rocket_launcher';
        $rocketLauncher->class_name = 'rocketLauncher';
        $rocketLauncher->description = 'The rocket launcher is a simple, cost-effective defensive option.';
        $rocketLauncher->description_long = 'Your first basic line of defense. These are simple ground based launch facilities that fire conventional warhead tipped missiles at attacking enemy targets. As they are cheap to construct and no research is required, they are well suited for defending raids, but lose effectiveness defending from larger scale attacks. Once you begin construction on more advanced defense weapons systems, Rocket Launchers become simple fodder to allow your more damaging weapons to inflict greater damage for a longer period of time.
            
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $lightLaser->title = 'Light Laser';
        $lightLaser->machine_name = 'light_laser';
        $lightLaser->class_name = 'laserCannonLight';
        $lightLaser->description = 'Concentrated firing at a target with photons can produce significantly greater damage than standard ballistic weapons.';
        $lightLaser->description_long = 'As technology developed and more sophisticated ships were created, it was determined that a stronger line of defense was needed to counter the attacks. As Laser Technology advanced, a new weapon was designed to provide the next level of defense. Light Lasers are simple ground based weapons that utilize special targeting systems to track the enemy and fire a high intensity laser designed to cut through the hull of the target. In order to be kept cost effective, they were fitted with an improved shielding system, however the structural integrity is the same as that of the Rocket Launcher.
        
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $heavyLaser->title = 'Heavy Laser';
        $heavyLaser->machine_name = 'heavy_laser';
        $heavyLaser->class_name = 'laserCannonHeavy';
        $heavyLaser->description = 'The heavy laser is the logical development of the light laser.';
        $heavyLaser->description_long = 'The Heavy Laser is a practical, improved version of the Light Laser. Being more balanced than the Light Laser with improved alloy composition, it utilizes stronger, more densely packed beams, and even better onboard targeting systems.
        
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $gaussCannon->title = 'Gauss Cannon';
        $gaussCannon->machine_name = 'gauss_cannon';
        $gaussCannon->class_name = 'gaussCannon';
        $gaussCannon->description = 'The Gauss Cannon fires projectiles weighing tons at high speeds.';

        $gaussCannon->description_long = 'For a long time projectile weapons were regarded as antiquated in the wake of modern thermonuclear and energy technology and due to the development of the hyperdrive and improved armour. That was until the exact energy technology that had once aged it, helped it to re-achieve their established position.
        A gauss cannon is a large version of the particle accelerator. Extremely heavy missiles are accelerated with a huge electromagnetic force and have muzzle velocities that make the dirt surrounding the missile burn in the skies. This weapon is so powerful when fired that it creates a sonic boom. Modern armour and shields can barely withstand the force, often the target is completely penetrated by the power of the missile. Defense structures deactivate as soon as they have been too badly damaged.
        
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $ionCannon->title = 'Ion Cannon';
        $ionCannon->machine_name = 'ion_cannon';
        $ionCannon->class_name = 'ionCannon';
        $ionCannon->description = 'The Ion Cannon fires a continuous beam of accelerating ions, causing considerable damage to objects it strikes.';
        $ionCannon->description_long = 'An ion cannon is a weapon that fires beams of ions (positively or negatively charged particles). The Ion Cannon is actually a type of Particle Cannon; only the particles used are ionized. Due to their electrical charges, they also have the potential to disable electronic devices, and anything else that has an electrical or similar power source, using a phenomena known as the the Electromagetic Pulse (EMP effect). Due to the cannons highly improved shielding system, this cannon provides improved protection for your larger, more destructive defense weapons.
        
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $plasmaTurret->title = 'Plasma Turret';
        $plasmaTurret->machine_name = 'plasma_turret';
        $plasmaTurret->class_name = 'plasmaCannon';
        $plasmaTurret->description = 'Plasma Turrets release the energy of a solar flare and surpass even the destroyer in destructive effect.';

        $plasmaTurret->description_long = 'One of the most advanced defense weapons systems ever developed, the Plasma Turret uses a large nuclear reactor fuel cell to power an electromagnetic accelerator that fires a pulse, or toroid, of plasma. During operation, the Plasma turret first locks on a target and begins the process of firing. A plasma sphere is created in the turrets core by super heating and compressing gases, stripping them of their ions. Once the gas is superheated, compressed, and a plasma sphere is created, it is then loaded into the electromagnetic accelerator which is energized. Once fully energized, the accelerator is activated, which results in the plasma sphere being launched at an extremely high rate of speed to the intended target. From the targets perspective, the approaching bluish ball of plasma is impressive, but once it strikes, it causes instant destruction.
        
        Defensive facilities deactivate as soon as they are too heavily damaged. After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $smallShieldDome->title = 'Small Shield Dome';
        $smallShieldDome->machine_name = 'small_shield_dome';
        $smallShieldDome->class_name = 'shieldDomeSmall';
        $smallShieldDome->description = 'The small shield dome covers an entire planet with a field which can absorb a tremendous amount of energy.';
        $smallShieldDome->description_long = 'Colonizing new worlds brought about a new danger, space debris. A large asteroid could easily wipe out the world and all inhabitants. Advancements in shielding technology provided scientists with a way to develop a shield to protect an entire planet not only from space debris but, as it was learned, from an enemy attack. By creating a large electromagnetic field around the planet, space debris that would normally have destroyed the planet was deflected, and attacks from enemy Empires were thwarted. The first generators were large and the shield provided moderate protection, but it was later discovered that small shields did not afford the protection from larger scale attacks. The small shield dome was the prelude to a stronger, more advanced planetary shielding system to come.

After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $largeShieldDome->title = 'Large Shield Dome';
        $largeShieldDome->machine_name = 'large_shield_dome';
        $largeShieldDome->class_name = 'shieldDomeLarge';
        $largeShieldDome->description = 'The evolution of the small shield dome can employ significantly more energy to withstand attacks.';
        $largeShieldDome->description_long = 'The Large Shield Dome is the next step in the advancement of planetary shields, it is the result of years of work improving the Small Shield Dome. Built to withstand a larger barrage of enemy fire by providing a higher energized electromagnetic field, large domes provide a longer period of protection before collapsing.
        
        After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.';
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
        $antiBallisticMissile->title = 'Anti-Ballistic Missiles';
        $antiBallisticMissile->machine_name = 'anti_ballistic_missile';
        $antiBallisticMissile->class_name = 'missileInterceptor';
        $antiBallisticMissile->description = 'Anti-Ballistic Missiles destroy attacking interplanetary missiles';

        $antiBallisticMissile->description_long = 'Anti Ballistic Missiles (ABM) are your only line of defense when attacked by Interplanetary Missiles (IPM) on your planet or moon. When a launch of IPMs is detected, these missiles automatically arm, process a launch code in their flight computers, target the inbound IPM, and launch to intercept. During the flight, the target IPM is constantly tracked and course corrections are applied until the ABM reaches the target and destroys the attacking IPM. Each ABM destroys one incoming IPM.';
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
        $interplanetaryMissile->title = 'Interplanetary Missiles';
        $interplanetaryMissile->machine_name = 'interplanetary_missile';
        $interplanetaryMissile->class_name = 'missileInterplanetary';
        $interplanetaryMissile->description = 'Interplanetary Missiles destroy enemy defenses. Your interplanetary missiles have got a coverage of ?? systems.';

        $interplanetaryMissile->description_long = 'Interplanetary Missiles (IPM) are your offensive weapon to destroy the defenses of your target. Using state of the art tracking technology, each missile targets a certain number of defenses for destruction. Tipped with an anti-matter bomb, they deliver a destructive force so severe that destroyed shields and defenses cannot be repaired. The only way to counter these missiles is with ABMs.';
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

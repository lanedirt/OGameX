<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectProperties;
use OGame\GameObjects\Models\Fields\GameObjectRapidfire;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\Fields\GameObjectSpeedUpgrade;
use OGame\GameObjects\Models\ShipObject;

class MilitaryShipObjects
{
    /**
     * Returns all military ship objects.
     *
     * @return array<ShipObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Light Fighter ---
        $lightFighter = new ShipObject();
        $lightFighter->id = 204;
        $lightFighter->title = __('t_resources.light_fighter.title');
        $lightFighter->machine_name = 'light_fighter';
        $lightFighter->class_name = 'fighterLight';
        $lightFighter->description = __('t_resources.light_fighter.description');
        $lightFighter->description_long = __('t_resources.light_fighter.description_long');
        $lightFighter->requirements = [
            new GameObjectRequirement('shipyard', 1),
            new GameObjectRequirement('combustion_drive', 1),
        ];
        $lightFighter->price = new GameObjectPrice(3000, 1000, 0, 0);
        $lightFighter->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $lightFighter->properties = new GameObjectProperties($lightFighter, 4000, 10, 50, 12500, 50, 20);
        $lightFighter->assets = new GameObjectAssets();
        $lightFighter->assets->imgSmall = 'light_fighter_small.jpg';
        $lightFighter->assets->imgMicro = 'light_fighter_small.jpg';
        $buildingObjectsNew[] = $lightFighter;

        // --- Heavy Fighter ---
        $heavyFighter = new ShipObject();
        $heavyFighter->id = 205;
        $heavyFighter->title = __('t_resources.heavy_fighter.title');
        $heavyFighter->machine_name = 'heavy_fighter';
        $heavyFighter->class_name = 'fighterHeavy';
        $heavyFighter->description = __('t_resources.heavy_fighter.description');
        $heavyFighter->description_long = __('t_resources.heavy_fighter.description_long');
        $heavyFighter->requirements = [
            new GameObjectRequirement('shipyard', 3),
            new GameObjectRequirement('armor_technology', 2),
            new GameObjectRequirement('impulse_drive', 2),
        ];
        $heavyFighter->price = new GameObjectPrice(6000, 4000, 0, 0);
        $heavyFighter->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('small_cargo', 3),
            new GameObjectRapidfire('crawler', 5),
        ];
        $heavyFighter->properties = new GameObjectProperties($heavyFighter, 10000, 25, 150, 10000, 100, 75);
        $heavyFighter->assets = new GameObjectAssets();
        $heavyFighter->assets->imgSmall = 'heavy_fighter_small.jpg';
        $heavyFighter->assets->imgMicro = 'heavy_fighter_small.jpg';
        $buildingObjectsNew[] = $heavyFighter;

        // --- Cruiser ---
        $cruiser = new ShipObject();
        $cruiser->id = 206;
        $cruiser->title = __('t_resources.cruiser.title');
        $cruiser->machine_name = 'cruiser';
        $cruiser->class_name = 'cruiser';
        $cruiser->description = __('t_resources.cruiser.description');
        $cruiser->description_long = __('t_resources.cruiser.description_long');

        $cruiser->requirements = [
            new GameObjectRequirement('shipyard', 5),
            new GameObjectRequirement('impulse_drive', 4),
            new GameObjectRequirement('ion_technology', 2),
        ];
        $cruiser->price = new GameObjectPrice(20000, 7000, 2000, 0);

        $cruiser->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('light_fighter', 6),
            new GameObjectRapidfire('rocket_launcher', 10),
            new GameObjectRapidfire('crawler', 5),
        ];
        $cruiser->properties = new GameObjectProperties($cruiser, 27000, 50, 400, 15000, 800, 300);
        $cruiser->assets = new GameObjectAssets();
        $cruiser->assets->imgSmall = 'cruiser_small.jpg';
        $cruiser->assets->imgMicro = 'cruiser_small.jpg';
        $buildingObjectsNew[] = $cruiser;

        // --- Battleship ---
        $battleship = new ShipObject();
        $battleship->id = 207;
        $battleship->title = __('t_resources.battle_ship.title');
        $battleship->machine_name = 'battle_ship';
        $battleship->class_name = 'battleship';
        $battleship->description = __('t_resources.battle_ship.description');
        $battleship->description_long = __('t_resources.battle_ship.description_long');
        $battleship->requirements = [
            new GameObjectRequirement('shipyard', 7),
            new GameObjectRequirement('hyperspace_drive', 4),
        ];
        $battleship->price = new GameObjectPrice(45000, 15000, 0, 0);
        $battleship->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $battleship->properties = new GameObjectProperties($battleship, 60000, 200, 1000, 10000, 1500, 500);
        $battleship->assets = new GameObjectAssets();
        $battleship->assets->imgSmall = 'battleship_small.jpg';
        $battleship->assets->imgMicro = 'battleship_small.jpg';
        $buildingObjectsNew[] = $battleship;

        // --- Battlecruiser ---
        $battlecruiser = new ShipObject();
        $battlecruiser->id = 215;
        $battlecruiser->title = __('t_resources.battlecruiser.title');
        $battlecruiser->machine_name = 'battlecruiser';
        $battlecruiser->class_name = 'interceptor';
        $battlecruiser->description = __('t_resources.battlecruiser.description');
        $battlecruiser->description_long = __('t_resources.battlecruiser.description_long');
        $battlecruiser->requirements = [
            new GameObjectRequirement('shipyard', 8),
            new GameObjectRequirement('hyperspace_drive', 5),
            new GameObjectRequirement('hyperspace_technology', 5),
            new GameObjectRequirement('laser_technology', 12),
        ];
        $battlecruiser->price = new GameObjectPrice(30000, 40000, 15000, 0);
        $battlecruiser->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('heavy_fighter', 4),
            new GameObjectRapidfire('cruiser', 4),
            new GameObjectRapidfire('battle_ship', 7),
            new GameObjectRapidfire('small_cargo', 3),
            new GameObjectRapidfire('large_cargo', 3),
            new GameObjectRapidfire('crawler', 5),
        ];
        $battlecruiser->properties = new GameObjectProperties($battlecruiser, 70000, 400, 700, 10000, 750, 250);
        $battlecruiser->assets = new GameObjectAssets();
        $battlecruiser->assets->imgSmall = 'battlecruiser_small.jpg';
        $battlecruiser->assets->imgMicro = 'battlecruiser_small.jpg';
        $buildingObjectsNew[] = $battlecruiser;

        // --- Bomber ---
        $bomber = new ShipObject();
        $bomber->id = 211;
        $bomber->title = __('t_resources.bomber.title');
        $bomber->machine_name = 'bomber';
        $bomber->class_name = 'bomber';
        $bomber->description = __('t_resources.bomber.description');
        $bomber->description_long = __('t_resources.bomber.description_long');
        $bomber->requirements = [
            new GameObjectRequirement('shipyard', 8),
            new GameObjectRequirement('impulse_drive', 6),
            new GameObjectRequirement('plasma_technology', 5),
        ];
        $bomber->price = new GameObjectPrice(50000, 25000, 15000, 0);

        $bomber->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('rocket_launcher', 20),
            new GameObjectRapidfire('light_laser', 20),
            new GameObjectRapidfire('heavy_laser', 10),
            new GameObjectRapidfire('ion_cannon', 10),
            new GameObjectRapidfire('gauss_cannon', 5),
            new GameObjectRapidfire('plasma_turret', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $bomber->properties = new GameObjectProperties($bomber, 75000, 500, 1000, 4000, 500, 700);
        $bomber->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('hyperspace_drive', 8, 5000),
        ];
        $bomber->assets = new GameObjectAssets();
        $bomber->assets->imgSmall = 'bomber_small.jpg';
        $bomber->assets->imgMicro = 'bomber_small.jpg';
        $buildingObjectsNew[] = $bomber;

        // --- Destroyer ---
        $destroyer = new ShipObject();
        $destroyer->id = 213;
        $destroyer->title = __('t_resources.destroyer.title');
        $destroyer->machine_name = 'destroyer';
        $destroyer->class_name = 'destroyer';
        $destroyer->description = __('t_resources.destroyer.description');
        $destroyer->description_long = __('t_resources.destroyer.description_long');
        $destroyer->requirements = [
            new GameObjectRequirement('shipyard', 9),
            new GameObjectRequirement('hyperspace_drive', 6),
            new GameObjectRequirement('hyperspace_technology', 5),
        ];
        $destroyer->price = new GameObjectPrice(60000, 50000, 15000, 0);
        $destroyer->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('light_laser', 10),
            new GameObjectRapidfire('battlecruiser', 2),
            new GameObjectRapidfire('crawler', 5),
        ];
        $destroyer->properties = new GameObjectProperties($destroyer, 110000, 500, 2000, 5000, 2000, 1000);
        $destroyer->assets = new GameObjectAssets();
        $destroyer->assets->imgSmall = 'destroyer_small.jpg';
        $destroyer->assets->imgMicro = 'destroyer_small.jpg';
        $buildingObjectsNew[] = $destroyer;

        // --- Deathstar ---
        $deathstar = new ShipObject();
        $deathstar->id = 214;
        $deathstar->title = __('t_resources.deathstar.title');
        $deathstar->machine_name = 'deathstar';
        $deathstar->class_name = 'deathstar';
        $deathstar->description = __('t_resources.deathstar.description');
        $deathstar->description_long = __('t_resources.deathstar.description_long');

        $deathstar->requirements = [
            new GameObjectRequirement('shipyard', 12),
            new GameObjectRequirement('hyperspace_drive', 7),
            new GameObjectRequirement('hyperspace_technology', 6),
            new GameObjectRequirement('graviton_technology', 1),
        ];
        $deathstar->price = new GameObjectPrice(5000000, 4000000, 1000000, 0);
        $deathstar->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 250),
            new GameObjectRapidfire('solar_satellite', 250),
            new GameObjectRapidfire('light_fighter', 200),
            new GameObjectRapidfire('heavy_fighter', 100),
            new GameObjectRapidfire('cruiser', 33),
            new GameObjectRapidfire('battle_ship', 30),
            new GameObjectRapidfire('bomber', 25),
            new GameObjectRapidfire('destroyer', 5),
            new GameObjectRapidfire('small_cargo', 250),
            new GameObjectRapidfire('large_cargo', 250),
            new GameObjectRapidfire('colony_ship', 250),
            new GameObjectRapidfire('recycler', 250),
            new GameObjectRapidfire('rocket_launcher', 200),
            new GameObjectRapidfire('light_laser', 200),
            new GameObjectRapidfire('heavy_laser', 100),
            new GameObjectRapidfire('ion_cannon', 100),
            new GameObjectRapidfire('gauss_cannon', 50),
            new GameObjectRapidfire('battlecruiser', 15),
            new GameObjectRapidfire('crawler', 250),
        ];
        $deathstar->properties = new GameObjectProperties($deathstar, 9000000, 50000, 200000, 100, 1000000, 1);
        $deathstar->assets = new GameObjectAssets();
        $deathstar->assets->imgSmall = 'deathstar_small.jpg';
        $deathstar->assets->imgMicro = 'deathstar_small.jpg';
        $buildingObjectsNew[] = $deathstar;

        // --- Reaper ---
        $reaper = new ShipObject();
        $reaper->id = 218;
        $reaper->title = __('t_resources.reaper.title');
        $reaper->machine_name = 'reaper';
        $reaper->class_name = 'reaper';
        $reaper->description = __('t_resources.reaper.description');
        $reaper->description_long = __('t_resources.reaper.description_long');
        $reaper->requirements = [
            new GameObjectRequirement('shipyard', 6),
            new GameObjectRequirement('impulse_drive', 6),
            new GameObjectRequirement('hyperspace_drive', 4),
            new GameObjectRequirement('weapon_technology', 8),
            new GameObjectRequirement('shielding_technology', 6),
        ];
        $reaper->price = new GameObjectPrice(85000, 55000, 20000, 0);
        $reaper->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('light_fighter', 4),
            new GameObjectRapidfire('heavy_fighter', 3),
            new GameObjectRapidfire('small_cargo', 3),
            new GameObjectRapidfire('large_cargo', 3),
            new GameObjectRapidfire('crawler', 5),
        ];
        $reaper->properties = new GameObjectProperties($reaper, 140000, 700, 2800, 7000, 10000, 1100);
        // Use hyperspace drive
        $reaper->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('hyperspace_drive', 4),
        ];

        $reaper->assets = new GameObjectAssets();
        $reaper->assets->imgSmall = 'reaper_small.jpg';
        $reaper->assets->imgMicro = 'reaper_micro.jpg';
        $buildingObjectsNew[] = $reaper;

        return $buildingObjectsNew;
    }
}

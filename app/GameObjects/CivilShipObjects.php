<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectProduction;
use OGame\GameObjects\Models\Fields\GameObjectProperties;
use OGame\GameObjects\Models\Fields\GameObjectRapidfire;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\Fields\GameObjectSpeedUpgrade;
use OGame\GameObjects\Models\ShipObject;
use OGame\Models\Enums\PlanetType;

class CivilShipObjects
{
    /**
     * Returns all civil ships objects.
     *
     * @return array<ShipObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Small Cargo ---
        $smallCargo = new ShipObject();
        $smallCargo->id = 202;
        $smallCargo->title = __('t_resources.small_cargo.title');
        $smallCargo->machine_name = 'small_cargo';
        $smallCargo->class_name = 'transporterSmall';
        $smallCargo->description = __('t_resources.small_cargo.description');
        $smallCargo->description_long = __('t_resources.small_cargo.description_long');
        $smallCargo->requirements = [
            new GameObjectRequirement('shipyard', 2),
            new GameObjectRequirement('combustion_drive', 2),
        ];
        $smallCargo->price = new GameObjectPrice(2000, 2000, 0, 0);
        $smallCargo->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $smallCargo->properties = new GameObjectProperties($smallCargo, 4000, 10, 5, 5000, 5000, 10);
        // Switch to Impulse at 5 and bump base speed to 10,000
        $smallCargo->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('impulse_drive', 5),
        ];
        $smallCargo->assets = new GameObjectAssets();
        $smallCargo->assets->imgSmall = 'small_cargo_small.jpg';
        $smallCargo->assets->imgMicro = 'small_cargo_micro.jpg';
        $buildingObjectsNew[] = $smallCargo;

        // --- Large Cargo ---
        $largeCargo = new ShipObject();
        $largeCargo->id = 203;
        $largeCargo->title = __('t_resources.large_cargo.title');
        $largeCargo->machine_name = 'large_cargo';
        $largeCargo->class_name = 'transporterLarge';
        $largeCargo->description = __('t_resources.large_cargo.description');
        $largeCargo->description_long = __('t_resources.large_cargo.description_long');
        $largeCargo->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('combustion_drive', 6),
        ];
        $largeCargo->price = new GameObjectPrice(6000, 6000, 0, 0);
        $largeCargo->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $largeCargo->properties = new GameObjectProperties($largeCargo, 12000, 25, 5, 7500, 25000, 50);

        $largeCargo->assets = new GameObjectAssets();
        $largeCargo->assets->imgSmall = 'large_cargo_small.jpg';
        $largeCargo->assets->imgMicro = 'large_cargo_micro.jpg';
        $buildingObjectsNew[] = $largeCargo;

        // --- Colony Ship ---
        $colonyShip = new ShipObject();
        $colonyShip->id = 208;
        $colonyShip->title = __('t_resources.colony_ship.title');
        $colonyShip->machine_name = 'colony_ship';
        $colonyShip->class_name = 'colonyShip';
        $colonyShip->description = __('t_resources.colony_ship.description');
        $colonyShip->description_long = __('t_resources.colony_ship.description_long');
        $colonyShip->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('impulse_drive', 3),
        ];
        $colonyShip->price = new GameObjectPrice(10000, 20000, 10000, 0);
        $colonyShip->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $colonyShip->properties = new GameObjectProperties($colonyShip, 30000, 100, 50, 2500, 7500, 100);

        $colonyShip->assets = new GameObjectAssets();
        $colonyShip->assets->imgSmall = 'colony_ship_small.jpg';
        $colonyShip->assets->imgMicro = 'colony_ship_micro.jpg';
        $buildingObjectsNew[] = $colonyShip;

        // --- Recycler ---
        $recycler = new ShipObject();
        $recycler->id = 209;
        $recycler->title = __('t_resources.recycler.title');
        $recycler->machine_name = 'recycler';
        $recycler->class_name = 'recycler';
        $recycler->description = __('t_resources.recycler.description');
        $recycler->description_long = __('t_resources.recycler.description_long');
        $recycler->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('combustion_drive', 6),
            new GameObjectRequirement('shielding_technology', 2),
        ];
        $recycler->price = new GameObjectPrice(10000, 6000, 2000, 0);
        $recycler->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('crawler', 5),
        ];
        $recycler->properties = new GameObjectProperties($recycler, 16000, 10, 1, 2000, 20000, 300);
        // Switch to Impulse at 17 (base 4,000), then Hyperspace at 15 (base 6,000)
        $recycler->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('impulse_drive', 17),
            new GameObjectSpeedUpgrade('hyperspace_drive', 15),
        ];
        $recycler->assets = new GameObjectAssets();
        $recycler->assets->imgSmall = 'recycler_small.jpg';
        $recycler->assets->imgMicro = 'recycler_micro.jpg';
        $buildingObjectsNew[] = $recycler;

        // --- Espionage Probe ---
        $espionageProbe = new ShipObject();
        $espionageProbe->id = 210;
        $espionageProbe->title = __('t_resources.espionage_probe.title');
        $espionageProbe->machine_name = 'espionage_probe';
        $espionageProbe->class_name = 'espionageProbe';
        $espionageProbe->description = __('t_resources.espionage_probe.description');
        $espionageProbe->description_long = __('t_resources.espionage_probe.description_long');
        $espionageProbe->requirements = [
            new GameObjectRequirement('shipyard', 3),
            new GameObjectRequirement('combustion_drive', 3),
            new GameObjectRequirement('espionage_technology', 2),
        ];
        $espionageProbe->price = new GameObjectPrice(0, 1000, 0, 0);
        $espionageProbe->properties = new GameObjectProperties($espionageProbe, 1000, 0, 0, 100000000, 0, 1, 5);

        $espionageProbe->assets = new GameObjectAssets();
        $espionageProbe->assets->imgSmall = 'espionage_probe_small.jpg';
        $espionageProbe->assets->imgMicro = 'espionage_probe_micro.jpg';
        $buildingObjectsNew[] = $espionageProbe;

        // --- Solar Satellite ---
        $solarSatellite = new ShipObject();
        $solarSatellite->id = 212;
        $solarSatellite->title = __('t_resources.solar_satellite.title');
        $solarSatellite->machine_name = 'solar_satellite';
        $solarSatellite->class_name = 'solarSatellite';
        $solarSatellite->description = __('t_resources.solar_satellite.description');
        $solarSatellite->description_long = __('t_resources.solar_satellite.description_long');
        $solarSatellite->requirements = [
            new GameObjectRequirement('shipyard', 1),
        ];
        $solarSatellite->price = new GameObjectPrice(0, 2000, 500, 0);

        $solarSatellite->production = new GameObjectProduction();
        // TODO: solar satellite production formula should be dependent on the planet position: proximity to sun.
        $solarSatellite->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            floor(($gameObjectProduction->planetService->getPlanetTempMax() + 140) / 6) * $level;

        $solarSatellite->properties = new GameObjectProperties($solarSatellite, 2000, 1, 0, 0, 0, 1);

        $solarSatellite->assets = new GameObjectAssets();
        $solarSatellite->assets->imgSmall = 'solar_satellite_small.jpg';
        $solarSatellite->assets->imgMicro = 'solar_satellite_micro.jpg';
        $buildingObjectsNew[] = $solarSatellite;

        // --- Crawler ---
        $crawler = new ShipObject();
        $crawler->id = 217;
        $crawler->title = __('t_resources.crawler.title');
        $crawler->machine_name = 'crawler';
        $crawler->class_name = 'resbuggy'; // CSS uses 'resbuggy' for Crawler sprite
        $crawler->description = __('t_resources.crawler.description');
        $crawler->description_long = __('t_resources.crawler.description_long');
        $crawler->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('combustion_drive', 4),
            new GameObjectRequirement('armor_technology', 4),
            new GameObjectRequirement('laser_technology', 4),
        ];
        $crawler->price = new GameObjectPrice(2000, 2000, 1000, 0);
        $crawler->valid_planet_types = [PlanetType::Planet];
        $crawler->properties = new GameObjectProperties($crawler, 4000, 1, 1, 0, 0, 0);

        $crawler->assets = new GameObjectAssets();
        $crawler->assets->imgSmall = 'crawler_small.jpg';
        $crawler->assets->imgMicro = 'crawler_micro.jpg';
        $buildingObjectsNew[] = $crawler;

        // --- Pathfinder ---
        $pathfinder = new ShipObject();
        $pathfinder->id = 219;
        $pathfinder->title = __('t_resources.pathfinder.title');
        $pathfinder->machine_name = 'pathfinder';
        $pathfinder->class_name = 'explorer'; // CSS uses 'explorer' for Pathfinder sprite
        $pathfinder->description = __('t_resources.pathfinder.description');
        $pathfinder->description_long = __('t_resources.pathfinder.description_long');
        $pathfinder->requirements = [
            new GameObjectRequirement('shipyard', 5),
            new GameObjectRequirement('combustion_drive', 6),
            new GameObjectRequirement('shielding_technology', 4),
            new GameObjectRequirement('hyperspace_technology', 2),
        ];
        $pathfinder->price = new GameObjectPrice(8000, 15000, 8000, 0);
        $pathfinder->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
            new GameObjectRapidfire('small_cargo', 3),
            new GameObjectRapidfire('crawler', 5),
        ];
        $pathfinder->properties = new GameObjectProperties($pathfinder, 23000, 100, 200, 12000, 10000, 300);
        // Switch to Impulse at 3, then Hyperspace at 3
        $pathfinder->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('impulse_drive', 3),
            new GameObjectSpeedUpgrade('hyperspace_drive', 3),
        ];

        $pathfinder->assets = new GameObjectAssets();
        $pathfinder->assets->imgSmall = 'pathfinder_small.jpg';
        $pathfinder->assets->imgMicro = 'pathfinder_micro.jpg';
        $buildingObjectsNew[] = $pathfinder;

        return $buildingObjectsNew;
    }
}

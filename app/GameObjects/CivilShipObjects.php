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
        $smallCargo->title = 'Small Cargo';
        $smallCargo->machine_name = 'small_cargo';
        $smallCargo->class_name = 'transporterSmall';
        $smallCargo->description = 'The small cargo is an agile ship which can quickly transport resources to other planets.';
        $smallCargo->description_long = 'Transporters are about as large as fighters, yet they forego high-performance drives and on-board weaponry for gains in their freighting capacity. As a result, a transporter should only be sent into battles when it is accompanied by combat-ready ships.
        
        As soon as the Impulse Drive reaches research level 5, the small transporter travels with increased base speed and is geared with an Impulse Drive.';
        $smallCargo->requirements = [
            new GameObjectRequirement('shipyard', 2),
            new GameObjectRequirement('combustion_drive', 2),
        ];
        $smallCargo->price = new GameObjectPrice(2000, 2000, 0, 0);
        $smallCargo->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $smallCargo->properties = new GameObjectProperties($smallCargo, 4000, 10, 5, 5000, 5000, 10);
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
        $largeCargo->title = 'Large Cargo';
        $largeCargo->machine_name = 'large_cargo';
        $largeCargo->class_name = 'transporterLarge';
        $largeCargo->description = 'This cargo ship has a much larger cargo capacity than the small cargo, and is generally faster thanks to an improved drive.';
        $largeCargo->description_long = 'As time evolved, the raids on colonies resulted in larger and larger amounts of resources being captured. As a result, Small Cargos were being sent out in mass numbers to compensate for the larger captures. It was quickly learned that a new class of ship was needed to maximize resources captured in raids, yet also be cost
effective. After much development, the Large Cargo was born.

To maximize the resources that can be stored in the holds, this ship has little in the way of weapons or armor. Thanks to the highly developed combustion engine installed, it serves as the most economical resource supplier between planets, and most effective in raids on hostile worlds.';
        $largeCargo->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('combustion_drive', 6),
        ];
        $largeCargo->price = new GameObjectPrice(6000, 6000, 0, 0);
        $largeCargo->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $largeCargo->properties = new GameObjectProperties($largeCargo, 12000, 25, 5, 15000, 25000, 50);

        $largeCargo->assets = new GameObjectAssets();
        $largeCargo->assets->imgSmall = 'large_cargo_small.jpg';
        $largeCargo->assets->imgMicro = 'large_cargo_micro.jpg';
        $buildingObjectsNew[] = $largeCargo;

        // --- Colony Ship ---
        $colonyShip = new ShipObject();
        $colonyShip->id = 208;
        $colonyShip->title = 'Colony Ship';
        $colonyShip->machine_name = 'colony_ship';
        $colonyShip->class_name = 'colonyShip';
        $colonyShip->description = 'Vacant planets can be colonised with this ship.';
        $colonyShip->description_long = 'In the 20th Century, Man decided to go for the stars. First, it was landing on the Moon. After that, a space station was built. Mars was colonized soon afterwards. It was soon determined that our growth depended on colonizing other worlds. Scientists and engineers all over the world gathered together to develop mans greatest achievement ever. The Colony Ship is born.

This ship is used to prepare a newly discovered planet for colonization. Once it arrives at the destination, the ship is instantly transformed into habitual living space to assist in populating and mining the new world. The maximum number of planets is thereby determined by the progress in astrophysics research.Two new levels of Astrotechnology allow for the colonization of one additional planet.';
        $colonyShip->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('impulse_drive', 3),
        ];
        $colonyShip->price = new GameObjectPrice(10000, 20000, 10000, 0);
        $colonyShip->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $colonyShip->properties = new GameObjectProperties($colonyShip, 30000, 100, 50, 2500, 7500, 100);

        $colonyShip->assets = new GameObjectAssets();
        $colonyShip->assets->imgSmall = 'colony_ship_small.jpg';
        $colonyShip->assets->imgMicro = 'colony_ship_micro.jpg';
        $buildingObjectsNew[] = $colonyShip;

        // --- Recycler ---
        $recycler = new ShipObject();
        $recycler->id = 209;
        $recycler->title = 'Recycler';
        $recycler->machine_name = 'recycler';
        $recycler->class_name = 'recycler';
        $recycler->description = 'Recyclers are the only ships able to harvest debris fields floating in a planet`s orbit after combat.';
        $recycler->description_long = 'Combat in space took on ever larger scales. Thousands of ships were destroyed and the resources of their remains seemed to be lost to the debris fields forever. Normal cargo ships couldn`t get close enough to these fields without risking substantial damage.
A recent development in shield technologies efficiently bypassed this issue. A new class of ships were created that were similar to the Transporters: the Recyclers. Their efforts helped to gather the thought-lost resources and then salvage them. The debris no longer posed any real danger thanks to the new shields.

As soon as Impulse Drive research has reached level 17, Recyclers are refitted with Impulse Drives. As soon as Hyperspace Drive research has reached level 15, Recyclers are refitted with Hyperspace Drives.';
        $recycler->requirements = [
            new GameObjectRequirement('shipyard', 4),
            new GameObjectRequirement('combustion_drive', 6),
            new GameObjectRequirement('shielding_technology', 2),
        ];
        $recycler->price = new GameObjectPrice(10000, 6000, 2000, 0);
        $recycler->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $recycler->properties = new GameObjectProperties($recycler, 16000, 10, 1, 2000, 20000, 300);
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
        $espionageProbe->title = 'Espionage Probe';
        $espionageProbe->machine_name = 'espionage_probe';
        $espionageProbe->class_name = 'espionageProbe';
        $espionageProbe->description = 'Espionage probes are small, agile drones that provide data on fleets and planets over great distances.';
        $espionageProbe->description_long = 'Espionage probes are small, agile drones that provide data on fleets and planets. Fitted with specially designed engines, it allows them to cover vast distances in only a few minutes. Once in orbit around the target planet, they quickly collect data and transmit the report back via your Deep Space Network for evaluation. But there is a risk to the intelligent gathering aspect. During the time the report is transmitted back to your network, the signal can be detected by the target and the probes can be destroyed.';
        $espionageProbe->requirements = [
            new GameObjectRequirement('shipyard', 3),
            new GameObjectRequirement('combustion_drive', 3),
            new GameObjectRequirement('espionage_technology', 2),
        ];
        $espionageProbe->price = new GameObjectPrice(0, 1000, 0, 0);
        $espionageProbe->properties = new GameObjectProperties($espionageProbe, 1000, 0, 0, 100000000, 5, 1);

        $espionageProbe->assets = new GameObjectAssets();
        $espionageProbe->assets->imgSmall = 'espionage_probe_small.jpg';
        $espionageProbe->assets->imgMicro = 'espionage_probe_micro.jpg';
        $buildingObjectsNew[] = $espionageProbe;

        // --- Solar Satellite ---
        $solarSatellite = new ShipObject();
        $solarSatellite->id = 212;
        $solarSatellite->title = 'Solar Satellite';
        $solarSatellite->machine_name = 'solar_satellite';
        $solarSatellite->class_name = 'solarSatellite';
        $solarSatellite->description = 'Solar satellites are simple platforms of solar cells, located in a high, stationary orbit. They gather sunlight and transmit it to the ground station via laser. A solar satellite produces 25 energy on this planet.';

        $solarSatellite->description_long = 'Scientists discovered a method of transmitting electrical energy to the colony using specially designed satellites in a geosynchronous orbit. Solar Satellites gather solar energy and transmit it to a ground station using advanced laser technology. The efficiency of a solar satellite depends on the strength of the solar radiation it receives. In principle, energy production in orbits closer to the sun is greater than for planets in orbits distant from the sun.
        Due to their good cost/performance ratio solar satellites can solve a lot of energy problems. But beware: Solar satellites can be easily destroyed in battle.';
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

        return $buildingObjectsNew;
    }
}

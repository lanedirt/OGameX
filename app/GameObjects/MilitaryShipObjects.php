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
        $lightFighter->title = 'Light Fighter';
        $lightFighter->machine_name = 'light_fighter';
        $lightFighter->class_name = 'fighterLight';
        $lightFighter->description = 'This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defenses.';
        $lightFighter->description_long = 'This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable when it is on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defenses.';
        $lightFighter->requirements = [
            new GameObjectRequirement('shipyard', 1),
            new GameObjectRequirement('combustion_drive', 1),
        ];
        $lightFighter->price = new GameObjectPrice(3000, 1000, 0, 0);
        $lightFighter->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $lightFighter->properties = new GameObjectProperties($lightFighter, 4000, 10, 50, 12500, 50, 20);
        $lightFighter->assets = new GameObjectAssets();
        $lightFighter->assets->imgSmall = 'light_fighter_small.jpg';
        $lightFighter->assets->imgMicro = 'light_fighter_small.jpg';
        $buildingObjectsNew[] = $lightFighter;

        // --- Heavy Fighter ---
        $heavyFighter = new ShipObject();
        $heavyFighter->id = 205;
        $heavyFighter->title = 'Heavy Fighter';
        $heavyFighter->machine_name = 'heavy_fighter';
        $heavyFighter->class_name = 'fighterHeavy';
        $heavyFighter->description = 'This fighter is better armoured and has a higher attack strength than the light fighter.';
        $heavyFighter->description_long = 'In developing the heavy fighter, researchers reached a point at which conventional drives no longer provided sufficient performance. In order to move the ship optimally, the impulse drive was used for the first time. This increased the costs, but also opened new possibilities. By using this drive, there was more energy left for weapons and shields; in addition, high-quality materials were used for this new family of fighters. With these changes, the heavy fighter represents a new era in ship technology and is the basis for cruiser technology.

        Slightly larger than the light fighter, the heavy fighter has thicker hulls, providing more protection, and stronger weaponry.';
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
        ];
        $heavyFighter->properties = new GameObjectProperties($heavyFighter, 10000, 25, 150, 10000, 100, 75);
        $heavyFighter->assets = new GameObjectAssets();
        $heavyFighter->assets->imgSmall = 'heavy_fighter_small.jpg';
        $heavyFighter->assets->imgMicro = 'heavy_fighter_small.jpg';
        $buildingObjectsNew[] = $heavyFighter;

        // --- Cruiser ---
        $cruiser = new ShipObject();
        $cruiser->id = 206;
        $cruiser->title = 'Cruiser';
        $cruiser->machine_name = 'cruiser';
        $cruiser->class_name = 'cruiser';
        $cruiser->description = 'Cruisers are armoured almost three times as heavily as heavy fighters and have more than twice the firepower. In addition, they are very fast.';
        $cruiser->description_long = 'With the development of the heavy laser and the ion cannon, light and heavy fighters encountered an alarmingly high number of defeats that increased with each raid. Despite many modifications, weapons strength and armour changes, it could not be increased fast enough to effectively counter these new defensive measures. Therefore, it was decided to build a new class of ship that combined more armor and more firepower. As a result of years of research and development, the Cruiser was born.

        Cruisers are armored almost three times of that of the heavy fighters, and possess more than twice the firepower of any combat ship in existence. They also possess speeds that far surpassed any spacecraft ever made. For almost a century, cruisers dominated the universe. However, with the development of Gauss cannons and plasma turrets, their predominance ended. They are still used today against fighter groups, but not as predominantly as before.';

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
        ];
        $cruiser->properties = new GameObjectProperties($cruiser, 27000, 50, 400, 15000, 800, 300);
        $cruiser->assets = new GameObjectAssets();
        $cruiser->assets->imgSmall = 'cruiser_small.jpg';
        $cruiser->assets->imgMicro = 'cruiser_small.jpg';
        $buildingObjectsNew[] = $cruiser;

        // --- Battleship ---
        $battleship = new ShipObject();
        $battleship->id = 207;
        $battleship->title = 'Battleship';
        $battleship->machine_name = 'battle_ship';
        $battleship->class_name = 'battleship';
        $battleship->description = 'Battleships form the backbone of a fleet. Their heavy cannons, high speed, and large cargo holds make them opponents to be taken seriously.';
        $battleship->description_long = 'Once it became apparent that the cruiser was losing ground to the increasing number of defense structures it was facing, and with the loss of ships on missions at unacceptable levels, it was decided to build a ship that could face those same type of defense structures with as little loss as possible. After extensive development, the Battleship was born. Built to withstand the largest of battles, the Battleship features large cargo spaces, heavy cannons, and high hyperdrive speed. Once developed, it eventually turned out to be the backbone of every raiding Emperors fleet.';
        $battleship->requirements = [
            new GameObjectRequirement('shipyard', 7),
            new GameObjectRequirement('hyperspace_drive', 4),
        ];
        $battleship->price = new GameObjectPrice(45000, 15000, 0, 0);
        $battleship->rapidfire = [
            new GameObjectRapidfire('espionage_probe', 5),
            new GameObjectRapidfire('solar_satellite', 5),
        ];
        $battleship->properties = new GameObjectProperties($battleship, 60000, 200, 1000, 10000, 1500, 500);
        $battleship->assets = new GameObjectAssets();
        $battleship->assets->imgSmall = 'battleship_small.jpg';
        $battleship->assets->imgMicro = 'battleship_small.jpg';
        $buildingObjectsNew[] = $battleship;

        // --- Battlecruiser ---
        $battlecruiser = new ShipObject();
        $battlecruiser->id = 215;
        $battlecruiser->title = 'Battlecruiser';
        $battlecruiser->machine_name = 'battlecruiser';
        $battlecruiser->class_name = 'interceptor';
        $battlecruiser->description = 'The Battlecruiser is highly specialized in the interception of hostile fleets.';
        $battlecruiser->description_long = 'This ship is one of the most advanced fighting ships ever to be developed, and is particularly deadly when it comes to destroying attacking fleets. With its improved laser cannons on board and advanced Hyperspace engine, the Battlecruiser is a serious force to be dealt with in any attack. Due to the ships design and its large weapons system, the cargo holds had to be cut, but this is compensated for by the lowered fuel consumption.';
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
        ];
        $battlecruiser->properties = new GameObjectProperties($battlecruiser, 70000, 400, 700, 10000, 750, 250);
        $battlecruiser->assets = new GameObjectAssets();
        $battlecruiser->assets->imgSmall = 'battlecruiser_small.jpg';
        $battlecruiser->assets->imgMicro = 'battlecruiser_small.jpg';
        $buildingObjectsNew[] = $battlecruiser;

        // --- Bomber ---
        $bomber = new ShipObject();
        $bomber->id = 211;
        $bomber->title = 'Bomber';
        $bomber->machine_name = 'bomber';
        $bomber->class_name = 'bomber';
        $bomber->description = 'The bomber was developed especially to destroy the planetary defenses of a world.';
        $bomber->description_long = 'Over the centuries, as defenses were starting to get larger and more sophisticated, fleets were starting to be destroyed at an alarming rate. It was decided that a new ship was needed to break defenses to ensure maximum results. After years of research and development, the Bomber was created.

        Using laser-guided targeting equipment and Plasma Bombs, the Bomber seeks out and destroys any defense mechanism it can find. As soon as the hyperspace drive is developed to Level 8, the Bomber is retrofitted with the hyperspace engine and can fly at higher speeds.';
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
        ];
        $bomber->properties = new GameObjectProperties($bomber, 75000, 500, 1000, 4000, 500, 700);
        $bomber->properties->speed_upgrade = [
            new GameObjectSpeedUpgrade('hyperspace_drive', 8),
        ];
        $bomber->assets = new GameObjectAssets();
        $bomber->assets->imgSmall = 'bomber_small.jpg';
        $bomber->assets->imgMicro = 'bomber_small.jpg';
        $buildingObjectsNew[] = $bomber;

        // --- Destroyer ---
        $destroyer = new ShipObject();
        $destroyer->id = 213;
        $destroyer->title = 'Destroyer';
        $destroyer->machine_name = 'destroyer';
        $destroyer->class_name = 'destroyer';
        $destroyer->description = 'The destroyer is the king of the warships.';
        $destroyer->description_long = 'The Destroyer is the result of years of work and development. With the development of Deathstars, it was decided that a class of ship was needed to defend against such a massive weapon. Thanks to its improved homing sensors, multi-phalanx Ion cannons, Gauss Cannons and Plasma Turrets, the Destroyer
        turned out to be one of the most fearsome ships created.

        Because the destroyer is very large, its manoeuvrability is severely limited, which makes it more of a battle station than a fighting ship. The lack of manoeuvrability is made up for by its sheer firepower, but it also costs significant amounts of deuterium to build and operate.';
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
        ];
        $destroyer->properties = new GameObjectProperties($destroyer, 110000, 500, 2000, 5000, 2000, 1000);
        $destroyer->assets = new GameObjectAssets();
        $destroyer->assets->imgSmall = 'destroyer_small.jpg';
        $destroyer->assets->imgMicro = 'destroyer_small.jpg';
        $buildingObjectsNew[] = $destroyer;

        // --- Deathstar ---
        $deathstar = new ShipObject();
        $deathstar->id = 214;
        $deathstar->title = 'Deathstar';
        $deathstar->machine_name = 'deathstar';
        $deathstar->class_name = 'deathstar';
        $deathstar->description = 'The destructive power of the deathstar is unsurpassed.';
        $deathstar->description_long = 'The Deathstar is the most powerful ship ever created. This moon sized ship is the only ship that can be seen with the naked eye on the ground. By the time you spot it, unfortunately, it is too late to do anything.

        Armed with a gigantic graviton cannon, the most advanced weapons system ever created in the Universe, this massive ship has not only the capability of destroying entire fleets and defenses, but also has the capability of destroying entire moons. Only the most advanced empires have the capability to build a ship of this mammoth size.';

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
        ];
        $deathstar->properties = new GameObjectProperties($deathstar, 9000000, 50000, 200000, 100, 1000000, 1);
        $deathstar->assets = new GameObjectAssets();
        $deathstar->assets->imgSmall = 'deathstar_small.jpg';
        $deathstar->assets->imgMicro = 'deathstar_small.jpg';
        $buildingObjectsNew[] = $deathstar;

        return $buildingObjectsNew;
    }
}

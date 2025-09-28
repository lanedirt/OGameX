<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\BuildingObject;
use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectProduction;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\Fields\GameObjectStorage;
use OGame\Models\Enums\PlanetType;

class BuildingObjects
{
    /**
     * Returns all building objects.
     *
     * @return array<BuildingObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Metal Mine ---
        $metalMine = new BuildingObject();
        $metalMine->id = 1;
        $metalMine->title = __('Metal Mine');
        $metalMine->machine_name = 'metal_mine';
        $metalMine->class_name = 'metalMine';
        $metalMine->description = __('Used in the extraction of metal ore, metal mines are of primary importance to all emerging and established empires.');
        $metalMine->description_long = 'Metal is the primary resource used in the foundation of your Empire. At greater depths, the mines can produce more output of viable metal for use in the construction of buildings, ships, defense systems, and research. As the mines drill deeper, more energy is required for maximum production. As metal is the most abundant of all resources available, its value is considered to be the lowest of all resources for trading.';

        $metalMine->price = new GameObjectPrice(60, 15, 0, 0, 1.5);
        $metalMine->valid_planet_types = [PlanetType::Planet];

        $metalMine->production = new GameObjectProduction();
        $metalMine->production->metal_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            30 * $level * 1.1 ** $level;
        $metalMine->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            -10 * $level * 1.1 ** $level;

        $metalMine->assets = new GameObjectAssets();
        $metalMine->assets->imgMicro = 'metal_mine_micro.jpg';
        $metalMine->assets->imgSmall = 'metal_mine_small.jpg';
        $buildingObjectsNew[] = $metalMine;
        // --------------------

        // --- Crystal Mine ---
        $crystalMine = new BuildingObject();
        $crystalMine->id = 2;
        $crystalMine->title = __('Crystal Mine');
        $crystalMine->machine_name = 'crystal_mine';
        $crystalMine->class_name = 'crystalMine';
        $crystalMine->description = __('Crystals are the main resource used to build electronic circuits and form certain alloy compounds.');
        $crystalMine->description_long = 'Crystal mines supply the main resource used to produce electronic circuits and from certain alloy compounds. Mining crystal consumes some one and half times more energy than a mining metal, making crystal more valuable. Almost all ships and all buildings require crystal. Most crystals required to build spaceships, however, are very rare, and like metal can only be found at a certain depth. Therefore, building mines in deeper strata will increase the amount of crystal produced.';

        $crystalMine->price = new GameObjectPrice(48, 24, 0, 0, 1.6);
        $crystalMine->valid_planet_types = [PlanetType::Planet];

        $crystalMine->production = new GameObjectProduction();
        $crystalMine->production->crystal_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            20 * $level * 1.1 ** $level;
        $crystalMine->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            -10 * $level * 1.1 ** $level;

        $crystalMine->assets = new GameObjectAssets();
        $crystalMine->assets->imgMicro = 'crystal_mine_micro.jpg';
        $crystalMine->assets->imgSmall = 'crystal_mine_small.jpg';
        $buildingObjectsNew[] = $crystalMine;
        // --------------------

        // --- Deuterium Synthesizer ---
        $deuteriumSynthesizer = new BuildingObject();
        $deuteriumSynthesizer->id = 3;
        $deuteriumSynthesizer->title = 'Deuterium Synthesizer';
        $deuteriumSynthesizer->machine_name = 'deuterium_synthesizer';
        $deuteriumSynthesizer->class_name = 'deuteriumSynthesizer';
        $deuteriumSynthesizer->description = 'Deuterium Synthesizers draw the trace Deuterium content from the water on a planet.';
        $deuteriumSynthesizer->description_long = 'Deuterium is also called heavy hydrogen. It is a stable isotope of hydrogen with a natural abundance in the oceans of colonies of approximately one atom in 6500 of hydrogen (~154 PPM). Deuterium thus accounts for approximately 0.015% (on a weight basis, 0.030%) of all. Deuterium is processed by special synthesizers which can separate the water from the Deuterium using specially designed centrifuges. The upgrade of the synthesizer allows for increasing the amount of Deuterium deposits processed. Deuterium is used when carrying out sensor phalanx scans, viewing galaxies, as fuel for ships, and performing specialized research upgrades.';

        $deuteriumSynthesizer->price = new GameObjectPrice(225, 75, 0, 0, 1.5);
        $deuteriumSynthesizer->valid_planet_types = [PlanetType::Planet];

        $deuteriumSynthesizer->production = new GameObjectProduction();
        $deuteriumSynthesizer->production->deuterium_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            10 * $level * 1.1 ** $level * (1.44 - 0.004 * $gameObjectProduction->planetService->getPlanetTempAvg());
        $deuteriumSynthesizer->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            -20 * $level * 1.1 ** $level;

        $deuteriumSynthesizer->assets = new GameObjectAssets();
        $deuteriumSynthesizer->assets->imgMicro = 'deuterium_synthesizer_micro.jpg';
        $deuteriumSynthesizer->assets->imgSmall = 'deuterium_synthesizer_small.jpg';
        $buildingObjectsNew[] = $deuteriumSynthesizer;
        // --------------------

        // --- Solar Plant ---
        $solarPlant = new BuildingObject();
        $solarPlant->id = 4;
        $solarPlant->title = 'Solar Plant';
        $solarPlant->machine_name = 'solar_plant';
        $solarPlant->class_name = 'solarPlant';
        $solarPlant->description = 'Solar power plants absorb energy from solar radiation. All mines need energy to operate.';
        $solarPlant->description_long = 'Gigantic solar arrays are used to generate power for the mines and the deuterium synthesizer. As the solar plant is upgraded, the surface area of the photovoltaic cells covering the planet increases, resulting in a higher energy output across the power grids of your planet.';

        $solarPlant->price = new GameObjectPrice(75, 30, 0, 0, 1.5);
        $solarPlant->valid_planet_types = [PlanetType::Planet];

        $solarPlant->production = new GameObjectProduction();
        $solarPlant->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            20 * $level * 1.1 ** $level;

        $solarPlant->assets = new GameObjectAssets();
        $solarPlant->assets->imgMicro = 'solar_plant_micro.jpg';
        $solarPlant->assets->imgSmall = 'solar_plant_small.jpg';
        $buildingObjectsNew[] = $solarPlant;
        // --------------------

        // --- Fusion Reactor ---
        $fusionReactor = new BuildingObject();
        $fusionReactor->id = 12;
        $fusionReactor->title = 'Fusion Reactor';
        $fusionReactor->machine_name = 'fusion_plant';
        $fusionReactor->class_name = 'fusionPlant';
        $fusionReactor->description = 'The fusion reactor uses deuterium to produce energy.';
        $fusionReactor->description_long = 'In fusion power plants, hydrogen nuclei are fused into helium nuclei under enormous temperature and pressure, releasing tremendous amounts of energy. For each gram of Deuterium consumed, up to 41,32*10^-13 Joule of energy can be produced; with 1 g you are able to produce 172 MWh energy.

        Larger reactor complexes use more deuterium and can produce more energy per hour. The energy effect could be increased by researching energy technology.

        The energy production of the fusion plant is calculated like that:
        30 * [Level Fusion Plant] * (1,05 + [Level Energy Technology] * 0,01) ^ [Level Fusion Plant]';

        $fusionReactor->price = new GameObjectPrice(900, 360, 180, 0, 1.8);
        $fusionReactor->valid_planet_types = [PlanetType::Planet];

        $fusionReactor->requirements = [
            new GameObjectRequirement('deuterium_synthesizer', 5),
            new GameObjectRequirement('energy_technology', 3),
        ];
        $fusionReactor->production = new GameObjectProduction();
        $fusionReactor->production->deuterium_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            -10 * $level * 1.1 ** $level;
        $fusionReactor->production->energy_formula = fn (GameObjectProduction $gameObjectProduction, int $level) =>
            30 * $level * (1.05 + $gameObjectProduction->playerService->getResearchLevel('energy_technology') * 0.01) ** $level;

        $fusionReactor->assets = new GameObjectAssets();
        $fusionReactor->assets->imgMicro = 'fusion_plant_micro.jpg';
        $fusionReactor->assets->imgSmall = 'fusion_plant_small.jpg';
        $buildingObjectsNew[] = $fusionReactor;
        // --------------------

        // --- Metal Storage ---
        $metalStorage = new BuildingObject();
        $metalStorage->id = 22;
        $metalStorage->title = 'Metal Storage';
        $metalStorage->machine_name = 'metal_store';
        $metalStorage->class_name = 'metalStorage';
        $metalStorage->description = 'Provides storage for excess metal.';
        $metalStorage->description_long = 'This giant storage facility is used to store metal ore. Each level of upgrading increases the amount of metal ore that can be stored. If the stores are full, no further metal will be mined.

        The Metal Storage protects a certain percentage of the mine`s daily production (max. 10 percent).';

        $metalStorage->price = new GameObjectPrice(1000, 0, 0, 0, 2);

        $metalStorage->storage = new GameObjectStorage();
        $metalStorage->storage->metal = 'return  5000 * floor(2.5 * exp(20 * $object_level / 33));';

        $metalStorage->assets = new GameObjectAssets();
        $metalStorage->assets->imgMicro = 'metal_store_micro.jpg';
        $metalStorage->assets->imgSmall = 'metal_store_small.jpg';
        $buildingObjectsNew[] = $metalStorage;
        // --------------------

        // --- Crystal Storage ---
        $crystalStorage = new BuildingObject();
        $crystalStorage->id = 23;
        $crystalStorage->title = 'Crystal Storage';
        $crystalStorage->machine_name = 'crystal_store';
        $crystalStorage->class_name = 'crystalStorage';
        $crystalStorage->description = 'Provides storage for excess crystal.';

        $crystalStorage->description_long = 'The unprocessed crystal will be stored in these giant storage halls in the meantime. With each level of upgrade, it increases the amount of crystal can be stored. If the crystal stores are full, no further crystal will be mined.

        The Crystal Storage protects a certain percentage of the mine`s daily production (max. 10 percent).';
        $crystalStorage->price = new GameObjectPrice(1000, 500, 0, 0, 2);
        $crystalStorage->storage = new GameObjectStorage();
        $crystalStorage->storage->crystal = 'return  5000 * floor(2.5 * exp(20 * $object_level / 33));';

        $crystalStorage->assets = new GameObjectAssets();
        $crystalStorage->assets->imgMicro = 'crystal_store_micro.jpg';
        $crystalStorage->assets->imgSmall = 'crystal_store_small.jpg';
        $buildingObjectsNew[] = $crystalStorage;
        // --------------------

        // --- Deuterium Tank ---
        $deuteriumTank = new BuildingObject();
        $deuteriumTank->id = 24;
        $deuteriumTank->title = 'Deuterium Tank';
        $deuteriumTank->machine_name = 'deuterium_store';
        $deuteriumTank->class_name = 'deuteriumStorage';
        $deuteriumTank->description = 'Giant tanks for storing newly-extracted deuterium.';
        $deuteriumTank->description_long = 'The Deuterium tank is for storing newly-synthesized deuterium. Once it is processed by the synthesizer, it is piped into this tank for later use. With each upgrade of the tank, the total storage capacity is increased. Once the capacity is reached, no further Deuterium will be synthesized.

        The Deuterium Tank protects a certain percentage of the synthesizer`s daily production (max. 10 percent).';

        $deuteriumTank->price = new GameObjectPrice(1000, 1000, 0, 0, 2);
        $deuteriumTank->storage = new GameObjectStorage();
        $deuteriumTank->storage->deuterium = 'return  5000 * floor(2.5 * exp(20 * $object_level / 33));';

        $deuteriumTank->assets = new GameObjectAssets();
        $deuteriumTank->assets->imgMicro = 'deuterium_store_micro.jpg';
        $deuteriumTank->assets->imgSmall = 'deuterium_store_small.jpg';
        $buildingObjectsNew[] = $deuteriumTank;
        // --------------------

        return $buildingObjectsNew;
    }
}

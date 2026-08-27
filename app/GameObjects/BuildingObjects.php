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
        $metalMine->title = __('t_resources.metal_mine.title');
        $metalMine->machine_name = 'metal_mine';
        $metalMine->class_name = 'metalMine';
        $metalMine->description = __('t_resources.metal_mine.description');
        $metalMine->description_long = __('t_resources.metal_mine.description_long');

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
        $crystalMine->title = __('t_resources.crystal_mine.title');
        $crystalMine->machine_name = 'crystal_mine';
        $crystalMine->class_name = 'crystalMine';
        $crystalMine->description = __('t_resources.crystal_mine.description');
        $crystalMine->description_long = __('t_resources.crystal_mine.description_long');

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
        $deuteriumSynthesizer->title = __('t_resources.deuterium_synthesizer.title');
        $deuteriumSynthesizer->machine_name = 'deuterium_synthesizer';
        $deuteriumSynthesizer->class_name = 'deuteriumSynthesizer';
        $deuteriumSynthesizer->description = __('t_resources.deuterium_synthesizer.description');
        $deuteriumSynthesizer->description_long = __('t_resources.deuterium_synthesizer.description_long');

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
        $solarPlant->title = __('t_resources.solar_plant.title');
        $solarPlant->machine_name = 'solar_plant';
        $solarPlant->class_name = 'solarPlant';
        $solarPlant->description = __('t_resources.solar_plant.description');
        $solarPlant->description_long = __('t_resources.solar_plant.description_long');

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
        $fusionReactor->title = __('t_resources.fusion_plant.title');
        $fusionReactor->machine_name = 'fusion_plant';
        $fusionReactor->class_name = 'fusionPlant';
        $fusionReactor->description = __('t_resources.fusion_plant.description');
        $fusionReactor->description_long = __('t_resources.fusion_plant.description_long');

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
        $metalStorage->title = __('t_resources.metal_store.title');
        $metalStorage->machine_name = 'metal_store';
        $metalStorage->class_name = 'metalStorage';
        $metalStorage->description = __('t_resources.metal_store.description');
        $metalStorage->description_long = __('t_resources.metal_store.description_long');

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
        $crystalStorage->title = __('t_resources.crystal_store.title');
        $crystalStorage->machine_name = 'crystal_store';
        $crystalStorage->class_name = 'crystalStorage';
        $crystalStorage->description = __('t_resources.crystal_store.description');
        $crystalStorage->description_long = __('t_resources.crystal_store.description_long');
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
        $deuteriumTank->title = __('t_resources.deuterium_store.title');
        $deuteriumTank->machine_name = 'deuterium_store';
        $deuteriumTank->class_name = 'deuteriumStorage';
        $deuteriumTank->description = __('t_resources.deuterium_store.description');
        $deuteriumTank->description_long = __('t_resources.deuterium_store.description_long');

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

<?php

namespace OGame\GameObjects;

use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\StationObject;
use OGame\Models\Enums\PlanetType;

class StationObjects
{
    /**
     * Returns all station (facilities) objects.
     *
     * @return array<StationObject>
     */
    public static function get(): array
    {
        $buildingObjectsNew = [];

        // --- Robotics Factory ---
        $roboticsFactory = new StationObject();
        $roboticsFactory->id = 14;
        $roboticsFactory->title = __('t_resources.robot_factory.title');
        $roboticsFactory->machine_name = 'robot_factory';
        $roboticsFactory->class_name = 'roboticsFactory';
        $roboticsFactory->description = __('t_resources.robot_factory.description');
        $roboticsFactory->description_long = __('t_resources.robot_factory.description_long');
        $roboticsFactory->price = new GameObjectPrice(400, 120, 200, 0, 2);
        $roboticsFactory->assets = new GameObjectAssets();
        $roboticsFactory->assets->imgMicro = 'robot_factory_micro.jpg';
        $roboticsFactory->assets->imgSmall = 'robot_factory_small.jpg';

        $buildingObjectsNew[] = $roboticsFactory;

        // --- Shipyard ---
        $shipyard = new StationObject();
        $shipyard->id = 21;
        $shipyard->title = __('t_resources.shipyard.title');
        $shipyard->machine_name = 'shipyard';
        $shipyard->class_name = 'shipyard';
        $shipyard->description = __('t_resources.shipyard.description');
        $shipyard->description_long = __('t_resources.shipyard.description_long');
        $shipyard->requirements = [
            new GameObjectRequirement('robot_factory', 2),
        ];
        $shipyard->price = new GameObjectPrice(400, 200, 100, 0, 2);
        $shipyard->assets = new GameObjectAssets();
        $shipyard->assets->imgMicro = 'shipyard_micro.jpg';
        $shipyard->assets->imgSmall = 'shipyard_small.jpg';

        $buildingObjectsNew[] = $shipyard;

        // --- Research Lab ---
        $researchLab = new StationObject();
        $researchLab->id = 31;
        $researchLab->title = __('t_resources.research_lab.title');
        $researchLab->machine_name = 'research_lab';
        $researchLab->class_name = 'researchLaboratory';
        $researchLab->description = __('t_resources.research_lab.description');
        $researchLab->description_long = __('t_resources.research_lab.description_long');
        $researchLab->price = new GameObjectPrice(200, 400, 200, 0, 2);
        $researchLab->valid_planet_types = [PlanetType::Planet];
        $researchLab->assets = new GameObjectAssets();
        $researchLab->assets->imgMicro = 'research_lab_micro.jpg';
        $researchLab->assets->imgSmall = 'research_lab_small.jpg';

        $buildingObjectsNew[] = $researchLab;

        // --- Alliance Depot ---
        $allianceDepot = new StationObject();
        $allianceDepot->id = 34;
        $allianceDepot->title = __('t_resources.alliance_depot.title');
        $allianceDepot->machine_name = 'alliance_depot';
        $allianceDepot->class_name = 'allianceDepot';
        $allianceDepot->description = __('t_resources.alliance_depot.description');
        $allianceDepot->description_long = __('t_resources.alliance_depot.description_long');
        $allianceDepot->price = new GameObjectPrice(20000, 40000, 0, 0, 2);
        $allianceDepot->valid_planet_types = [PlanetType::Planet];
        $allianceDepot->assets = new GameObjectAssets();
        $allianceDepot->assets->imgMicro = 'alliance_depot_micro.jpg';
        $allianceDepot->assets->imgSmall = 'alliance_depot_small.jpg';

        $buildingObjectsNew[] = $allianceDepot;

        // --- Missile Silo ---
        $missileSilo = new StationObject();
        $missileSilo->id = 44;
        $missileSilo->title = __('t_resources.missile_silo.title');
        $missileSilo->machine_name = 'missile_silo';
        $missileSilo->class_name = 'missileSilo';
        $missileSilo->description = __('t_resources.missile_silo.description');
        $missileSilo->description_long = __('t_resources.missile_silo.description_long');
        $missileSilo->requirements = [
            new GameObjectRequirement('shipyard', 1)
        ];
        $missileSilo->price = new GameObjectPrice(20000, 20000, 1000, 0, 2);
        $missileSilo->assets = new GameObjectAssets();
        $missileSilo->assets->imgMicro = 'missile_silo_micro.jpg';
        $missileSilo->assets->imgSmall = 'missile_silo_small.jpg';

        $buildingObjectsNew[] = $missileSilo;

        // --- Nanite Factory ---
        $naniteFactory = new StationObject();
        $naniteFactory->id = 15;
        $naniteFactory->title = __('t_resources.nano_factory.title');
        $naniteFactory->machine_name = 'nano_factory';
        $naniteFactory->class_name = 'naniteFactory';
        $naniteFactory->description = __('t_resources.nano_factory.description');
        $naniteFactory->description_long = __('t_resources.nano_factory.description_long');
        $naniteFactory->requirements = [
            new GameObjectRequirement('computer_technology', 10),
            new GameObjectRequirement('robot_factory', 10),
        ];
        $naniteFactory->price = new GameObjectPrice(1000000, 500000, 100000, 0, 2);
        $naniteFactory->valid_planet_types = [PlanetType::Planet];
        $naniteFactory->assets = new GameObjectAssets();
        $naniteFactory->assets->imgMicro = 'nanite_factory_micro.jpg';
        $naniteFactory->assets->imgSmall = 'nanite_factory_small.jpg';

        $buildingObjectsNew[] = $naniteFactory;

        // --- Terraformer ---
        $terraformer = new StationObject();
        $terraformer->id = 33;
        $terraformer->title = __('t_resources.terraformer.title');
        $terraformer->machine_name = 'terraformer';
        $terraformer->class_name = 'terraformer';
        $terraformer->description = __('t_resources.terraformer.description');
        $terraformer->description_long = __('t_resources.terraformer.description_long');
        $terraformer->requirements = [
            new GameObjectRequirement('nano_factory', 1),
            new GameObjectRequirement('energy_technology', 12),
        ];
        $terraformer->price = new GameObjectPrice(0, 50000, 100000, 1000, 2);
        $terraformer->valid_planet_types = [PlanetType::Planet];
        $terraformer->assets = new GameObjectAssets();
        $terraformer->assets->imgMicro = 'terraformer_micro.jpg';
        $terraformer->assets->imgSmall = 'terraformer_small.jpg';

        $buildingObjectsNew[] = $terraformer;

        // --- Space Dock ---
        $spaceDock = new StationObject();
        $spaceDock->id = 36;
        $spaceDock->title = __('t_resources.space_dock.title');
        $spaceDock->machine_name = 'space_dock';
        $spaceDock->class_name = 'repairDock';
        $spaceDock->description = __('t_resources.space_dock.description');
        $spaceDock->description_long = __('t_resources.space_dock.description_long');
        $spaceDock->requirements = [
            new GameObjectRequirement('shipyard', 2),
        ];
        $spaceDock->price = new GameObjectPrice(200, 0, 50, 50, 2);
        $spaceDock->consumesPlanetField = false; // Space Dock floats in orbit and doesn't consume a field
        $spaceDock->valid_planet_types = [PlanetType::Planet];
        $spaceDock->assets = new GameObjectAssets();
        $spaceDock->assets->imgMicro = 'space_dock_micro.jpg';
        $spaceDock->assets->imgSmall = 'space_dock_small.jpg';

        $buildingObjectsNew[] = $spaceDock;

        // --- Lunar Base ---
        $lunarBase = new StationObject();
        $lunarBase->id = 41;
        $lunarBase->title = __('t_resources.lunar_base.title');
        $lunarBase->machine_name = 'lunar_base';
        $lunarBase->class_name = 'moonbase';
        $lunarBase->description = __('t_resources.lunar_base.description');
        $lunarBase->description_long = __('t_resources.lunar_base.description_long');
        $lunarBase->price = new GameObjectPrice(20000, 40000, 20000, 0, 2);
        $lunarBase->valid_planet_types = [PlanetType::Moon];
        $lunarBase->assets = new GameObjectAssets();
        $lunarBase->assets->imgMicro = 'lunar_base_micro.jpg';
        $lunarBase->assets->imgSmall = 'lunar_base_small.jpg';

        $buildingObjectsNew[] = $lunarBase;

        // --- Sensor Phalanx ---
        $sensorPhalanx = new StationObject();
        $sensorPhalanx->id = 42;
        $sensorPhalanx->title = __('t_resources.sensor_phalanx.title');
        $sensorPhalanx->machine_name = 'sensor_phalanx';
        $sensorPhalanx->class_name = 'sensorPhalanx';
        $sensorPhalanx->description = __('t_resources.sensor_phalanx.description');
        $sensorPhalanx->description_long = __('t_resources.sensor_phalanx.description_long');
        $sensorPhalanx->requirements = [
            new GameObjectRequirement('lunar_base', 1),
        ];
        $sensorPhalanx->price = new GameObjectPrice(20000, 40000, 10000, 0, 2);
        $sensorPhalanx->valid_planet_types = [PlanetType::Moon];
        $sensorPhalanx->assets = new GameObjectAssets();
        $sensorPhalanx->assets->imgMicro = 'sensor_phalanx_micro.jpg';
        $sensorPhalanx->assets->imgSmall = 'sensor_phalanx_small.jpg';

        $buildingObjectsNew[] = $sensorPhalanx;

        // --- Jump Gate ---
        $jumpGate = new StationObject();
        $jumpGate->id = 43;
        $jumpGate->title = __('t_resources.jump_gate.title');
        $jumpGate->machine_name = 'jump_gate';
        $jumpGate->class_name = 'jumpGate';
        $jumpGate->description = __('t_resources.jump_gate.description');
        $jumpGate->description_long = __('t_resources.jump_gate.description_long');
        $jumpGate->requirements = [
            new GameObjectRequirement('lunar_base', 1),
            new GameObjectRequirement('hyperspace_technology', level: 7),
        ];
        $jumpGate->price = new GameObjectPrice(2000000, 4000000, 2000000, 0, 2);
        $jumpGate->valid_planet_types = [PlanetType::Moon];
        $jumpGate->assets = new GameObjectAssets();
        $jumpGate->assets->imgMicro = 'jump_gate_micro.jpg';
        $jumpGate->assets->imgSmall = 'jump_gate_small.jpg';

        $buildingObjectsNew[] = $jumpGate;

        return $buildingObjectsNew;
    }
}

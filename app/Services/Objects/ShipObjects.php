<?php

namespace OGame\Services\Objects;

use OGame\Services\Objects\Models\Fields\GameObjectAssets;
use OGame\Services\Objects\Models\Fields\GameObjectPrice;
use OGame\Services\Objects\Models\Fields\GameObjectRequirement;
use OGame\Services\Objects\Models\ShipObject;

class ShipObjects
{
    /**
     * Returns all defined building objects.
     *
     * @return array<ShipObject>
     */
    public static function get() : array
    {
        $buildingObjectsNew = [];

        // --- Solar Satellite ---
        $solarSatellite = new ShipObject();
        $solarSatellite->id = 212;
        $solarSatellite->title = 'Solar Satellite';
        $solarSatellite->machine_name = 'solar_satellite';
        $solarSatellite->description = 'Solar satellites are simple platforms of solar cells, located in a high, stationary orbit. They gather sunlight and transmit it to the ground station via laser. A solar satellite produces 25 energy on this planet.';
        $solarSatellite->description_long = 'Scientists discovered a method of transmitting electrical energy to the colony using specially designed satellites in a geosynchronous orbit. Solar Satellites gather solar energy and transmit it to a ground station using advanced laser technology. The efficiency of a solar satellite depends on the strength of the solar radiation it receives. In principle, energy production in orbits closer to the sun is greater than for planets in orbits distant from the sun.
Due to their good cost/performance ratio solar satellites can solve a lot of energy problems. But beware: Solar satellites can be easily destroyed in battle.';
        $solarSatellite->requirements = [
            new GameObjectRequirement('shipyard', 1),
        ];
        $solarSatellite->price = new GameObjectPrice(0, 2000, 500, 0);
        /*$solarSatellite->properties = [
            'structural_integrity' => 2000,
            'shield' => 1,
            'attack' => 1,
            'speed' => 0,
            'capacity' => 0,
            'fuel' => 1,
        ];*/
        $solarSatellite->assets = new GameObjectAssets();
        $solarSatellite->assets->imgMicro = 'solar_satellite_small.jpg';
        $solarSatellite->assets->imgSmall = 'robot_factory_micro.jpg';
        $buildingObjectsNew[] = $solarSatellite;

        return $buildingObjectsNew;
    }
}
<?php

namespace OGame\Services\Objects\Models\Fields;

class GameObjectProduction
{
/*
 * 'production' => [
                    'metal' => 'return "0";',
                    'crystal' => 'return "0";',
                    'deuterium' => 'return ((10 * $building_level * pow((1.1), $building_level)) * (-0.002 * $planet_temperature + 1.28))  * (0.1 * $building_percentage);',
                    'energy' => 'return - (20 * $building_level * pow((1.1), $building_level)) * (0.1 * $building_percentage);',
                ],
 */

    public int $metal;
    public int $crystal;
    public int $deuterium;
    public int $energy;
}
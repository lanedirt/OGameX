<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class DebrisFieldHarvest extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'debris_field_harvest';
        $this->params = [
            'to',
            'coordinates',
            'ship_name',
            'ship_amount',
            'storage_capacity',
            'metal',
            'crystal',
            'deuterium',
            'harvested_metal',
            'harvested_crystal',
            'harvested_deuterium',
        ];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}

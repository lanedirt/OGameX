<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class PlanetRelocationSuccess extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'planet_relocation_success';
        $this->params = ['planet_name', 'old_coordinates', 'new_coordinates'];
        $this->tab = 'economy';
        $this->subtab = 'economy';
    }
}

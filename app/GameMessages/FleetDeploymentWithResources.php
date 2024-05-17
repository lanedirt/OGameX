<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class FleetDeploymentWithResources extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'fleet_deployment_with_resources';
        $this->params = ['from', 'to', 'metal', 'crystal', 'deuterium'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}

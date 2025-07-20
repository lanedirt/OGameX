<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionFailureAndFleetDestroyed1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failure_and_fleet_destroyed_1';
    }
}

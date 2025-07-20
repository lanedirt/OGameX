<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionResourcesFound2 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_resources_found_2';
    }
}

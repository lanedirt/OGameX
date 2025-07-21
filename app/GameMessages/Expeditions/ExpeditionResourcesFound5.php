<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionResourcesFoundGameMessage;

class ExpeditionResourcesFound5 extends ExpeditionResourcesFoundGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_resources_found_5';
    }
}

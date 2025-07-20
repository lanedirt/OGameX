<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionResourcesFound6 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_resources_found_6';
    }
}

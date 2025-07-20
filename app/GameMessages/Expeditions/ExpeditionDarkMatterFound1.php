<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionDarkMatterFound1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_dark_matter_found_1';
    }
}

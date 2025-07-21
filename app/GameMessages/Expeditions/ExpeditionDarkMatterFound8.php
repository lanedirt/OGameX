<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionDarkMatterFound8 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_dark_matter_found_8';
    }
}

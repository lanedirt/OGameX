<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionDarkMatterFound3 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_dark_matter_found_3';
    }
}

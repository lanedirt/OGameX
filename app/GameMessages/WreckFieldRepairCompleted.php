<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class WreckFieldRepairCompleted extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'wreck_field_repair_completed';
        $this->params = [
            'planet',
            'ship_count',
        ];
        $this->tab = 'economy';
        $this->subtab = 'economy';
    }
}

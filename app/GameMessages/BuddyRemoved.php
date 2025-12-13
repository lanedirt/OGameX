<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class BuddyRemoved extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'buddy_removed';
        $this->params = ['remover_name'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }
}

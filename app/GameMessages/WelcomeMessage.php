<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class WelcomeMessage extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'welcome_message';
        $this->params = ['player'];
        $this->tab = 'universe';
        $this->subtab = 'universe';
    }
}

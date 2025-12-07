<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class BuddyRequestReceived extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'buddy_request_received';
        $this->params = ['sender_name'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }
}

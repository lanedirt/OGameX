<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class BuddyRequestAccepted extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'buddy_request_accepted';
        $this->params = ['accepter_name'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }
}

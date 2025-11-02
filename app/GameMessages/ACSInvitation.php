<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ACSInvitation extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'acs_invitation';
        $this->params = ['inviter', 'acs_group_name', 'target_coordinates', 'arrival_time'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }
}

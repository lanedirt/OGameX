<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceMemberJoined extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_member_joined';
        $this->params = ['member_name'];
        $this->tab = 'alliance';
        $this->subtab = 'membership';
    }
}

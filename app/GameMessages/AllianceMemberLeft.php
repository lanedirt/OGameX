<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceMemberLeft extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_member_left';
        $this->params = ['member_name'];
        $this->tab = 'alliance';
        $this->subtab = 'membership';
    }
}

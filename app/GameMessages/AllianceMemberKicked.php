<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceMemberKicked extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_member_kicked';
        $this->params = ['alliance_name', 'alliance_tag'];
        $this->tab = 'alliance';
        $this->subtab = 'membership';
    }
}

<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class FleetUnionInvite extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'fleet_union_invite';
        $this->params = ['sender_name', 'union_name', 'target_player', 'target_coords', 'arrival_time'];
        $this->tab = 'fleets';
        $this->subtab = 'transport';
    }

    /**
     * Override getFrom to show the inviting player's name instead of a static string.
     */
    public function getFrom(): string
    {
        return $this->getParams()['sender_name'] ?? __('t_messages.' . $this->key . '.from');
    }
}

<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceBroadcast extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_broadcast';
        $this->params = ['sender_name', 'alliance_tag', 'message'];
        $this->tab = 'communication';
        $this->subtab = 'messages';
    }

    /**
     * Override getFrom to include params for dynamic sender name.
     *
     * @return string
     */
    public function getFrom(): string
    {
        // Check if all the params are provided
        if ($this->message->params === null) {
            $this->message->params = [];
        }

        $params = $this->checkParams($this->message->params);

        // Get the "from" field from the language files and replace params
        return __('t_messages.' . $this->key . '.from', $params);
    }
}

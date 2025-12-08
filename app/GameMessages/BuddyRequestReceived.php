<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class BuddyRequestReceived extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'buddy_request_received';
        $this->params = ['sender_name', 'buddy_request_id'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }

    /**
     * @inheritdoc
     */
    public function getFooterActions(): string
    {
        // Get the buddy request ID from the message params
        $buddyRequestId = $this->message->params['buddy_request_id'] ?? null;

        if (!$buddyRequestId) {
            return '';
        }

        return '
            <gradient-button sq30="">
                <button class="custom_btn tooltip acceptRequest" data-buddyid="' . $buddyRequestId . '" data-tooltip-title="Accept buddy request">
                    <span class="icon_nf icon_accept"></span>
                </button>
            </gradient-button>
            <gradient-button sq30="">
                <button class="custom_btn tooltip rejectRequest" data-buddyid="' . $buddyRequestId . '" data-tooltip-title="Reject buddy request">
                    <span class="icon_nf icon_refuse"></span>
                </button>
            </gradient-button>
        ';
    }
}

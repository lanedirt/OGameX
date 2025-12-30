<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionGainDarkMatter extends ExpeditionGameMessage
{
    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'expedition_gain_dark_matter';

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_gain_dark_matter.body.1
     * - t_messages.expedition_gain_dark_matter.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations = 8;

    /**
     * Overrides the body of the message to append the Dark Matter amount based on the params.
     */
    public function getBody(): string
    {
        // Get the base body with the random variation message.
        $translatedBody = parent::getBody();

        // Get the params to access the dark matter amount.
        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Append the Dark Matter amount to the body if the param is set.
        if (!empty($params['dark_matter_amount'])) {
            $translatedBody .= '<br /><br />' . __('t_messages.expedition_dark_matter_captured', ['dark_matter_amount' => $params['dark_matter_amount']]);
        }

        return $translatedBody;
    }
}

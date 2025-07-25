<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionFailedAndDelay extends ExpeditionGameMessage
{
    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'expedition_failed_and_delay';

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_failed_and_delay.body.1
     * - t_messages.expedition_failed_and_delay.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations = 3;
}

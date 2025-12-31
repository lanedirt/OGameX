<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionBattlePirates extends ExpeditionGameMessage
{
    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'expedition_battle_pirates';

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 7, then the following message keys should be added to the language files:
     * - t_messages.expedition_battle_pirates.body.1
     * - t_messages.expedition_battle_pirates.body.2
     * - ...
     * - t_messages.expedition_battle_pirates.body.7
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations = 7;
}

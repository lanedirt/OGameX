<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ExpeditionFailureAndSpeedup extends GameMessage
{
    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_failure_and_speedup.body.1
     * - t_messages.expedition_failure_and_speedup.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    private static int $numberOfVariations = 3;

    /**
     * The base key for the message.
     * @var string
     */
    private static string $baseKey = 'expedition_failure_and_speedup';

    protected function initialize(): void
    {
        $this->key = self::$baseKey;
        $this->params = [];
        $this->tab = 'fleets';
        $this->subtab = 'expeditions';
    }

    /**
     * Overides the body of the message to append the captured resource type and amount based on the params.
     */
    public function getBody(): string
    {
        // Change the body key to the correct random outcome message based on the params.
        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Get the message body from the language files with the correct variation number.
        $translatedBody = nl2br(__('t_messages.' . self::$baseKey . '.body.' . $params['message_variation_id'], $params));

        // Replace placeholders in translated body with actual values.
        $translatedBody = $this->replacePlaceholders($translatedBody);

        return $translatedBody;
    }

    /**
     * Get a random message variation id based on the number of possible message variations.
     * This is called by the expedition mission logic to set the message variation id for the to be sent message on mission processing.
     *
     * @return int
     */
    public static function getRandomMessageVariationId(): int
    {
        return random_int(1, self::$numberOfVariations);
    }
}

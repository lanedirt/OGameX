<?php

namespace OGame\GameMessages\Abstracts;

/**
 * This is the base class for all expedition game messages.
 * It contains the base functionality for all expedition game messages which includes showing the correct
 * message body variation based on the "message_variation_id" param.
 *
 * @package OGame\GameMessages\Abstracts
 * @author
 */
abstract class ExpeditionGameMessage extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = static::$baseKey;
        $this->params = [];
        $this->tab = 'fleets';
        $this->subtab = 'expeditions';
    }

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.[base_key].body.1
     * - t_messages.[base_key].body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations;

    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'placeholder';

    /**
     * Overides the body of the message to append the captured resource type and amount based on the params.
     */
    public function getBody(): string
    {
        // Change the body key to the correct random outcome message based on the params.
        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Get the message body from the language files with the correct variation number.
        $translatedBody = nl2br(__('t_messages.' . static::$baseKey . '.body.' . $params['message_variation_id'], $params));

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
        return random_int(1, static::$numberOfVariations);
    }

    /**
     * Get the number of variations for this expedition message.
     * This is used for testing to verify that all translation variations exist.
     *
     * @return int
     */
    public function getNumberOfVariations(): int
    {
        return static::$numberOfVariations;
    }

    /**
     * Get the base key for this expedition message.
     * This is used for testing to verify that the correct translation keys exist.
     *
     * @return string
     */
    public function getBaseKey(): string
    {
        return static::$baseKey;
    }
}

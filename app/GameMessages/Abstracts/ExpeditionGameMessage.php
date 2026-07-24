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
     * Number of message variations in each find-variant tier. Subclasses that support
     * variant messages set these so the body keys are laid out as:
     *   [1 .. normal] normal, then rare, then exceptional.
     * The three MUST sum to $numberOfVariations. Left at 0 for messages without tiers,
     * in which case getRandomMessageVariationIdForVariant() falls back to the full range.
     *
     * @var int
     */
    protected static int $normalVariations = 0;

    /**
     * @var int
     */
    protected static int $rareVariations = 0;

    /**
     * @var int
     */
    protected static int $exceptionalVariations = 0;

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
     * Get a random message variation id for a specific find variant (normal/rare/exceptional).
     * The variation ids are laid out in tiers (normal, then rare, then exceptional) sized by
     * $normalVariations / $rareVariations / $exceptionalVariations. Messages that do not define
     * tiers fall back to a random id across the full range.
     *
     * @param string $variant 'normal', 'rare', or 'exceptional'
     * @return int
     */
    public static function getRandomMessageVariationIdForVariant(string $variant): int
    {
        // No tiers configured: behave like a plain random variation.
        if (static::$normalVariations === 0) {
            return static::getRandomMessageVariationId();
        }

        return match ($variant) {
            'rare' => random_int(
                static::$normalVariations + 1,
                static::$normalVariations + static::$rareVariations
            ),
            'exceptional' => random_int(
                static::$normalVariations + static::$rareVariations + 1,
                static::$normalVariations + static::$rareVariations + static::$exceptionalVariations
            ),
            default => random_int(1, static::$normalVariations),
        };
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

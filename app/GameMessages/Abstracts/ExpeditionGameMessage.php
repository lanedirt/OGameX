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
     * Message variation ids belonging to each find-variant tier. Subclasses that support
     * variant messages set these to the explicit body key ids that make up each tier.
     * Ids are append-only and stable: message_variation_id is persisted on every sent
     * message, so an existing id must keep its original text forever and new messages
     * must be added at the end of the range. The three lists combined MUST cover exactly
     * the ids 1..$numberOfVariations. Left empty for messages without tiers, in which
     * case getRandomMessageVariationIdForVariant() falls back to the full range.
     *
     * @var list<int>
     */
    protected static array $normalVariationIds = [];

    /**
     * @var list<int>
     */
    protected static array $rareVariationIds = [];

    /**
     * @var list<int>
     */
    protected static array $exceptionalVariationIds = [];

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
     * A random id is picked from the matching tier id list ($normalVariationIds /
     * $rareVariationIds / $exceptionalVariationIds). Messages that do not define tiers
     * fall back to a random id across the full range.
     *
     * @param string $variant 'normal', 'rare', or 'exceptional'
     * @return int
     */
    public static function getRandomMessageVariationIdForVariant(string $variant): int
    {
        $ids = match ($variant) {
            'rare' => static::$rareVariationIds,
            'exceptional' => static::$exceptionalVariationIds,
            default => static::$normalVariationIds,
        };

        // No tiers configured (or an empty tier): behave like a plain random variation.
        if ($ids === []) {
            return static::getRandomMessageVariationId();
        }

        return $ids[array_rand($ids)];
    }

    /**
     * Get the message variation ids for each find-variant tier.
     * This is used for testing to verify that the tier lists combined cover exactly
     * the full range of variations.
     *
     * @return array{normal: list<int>, rare: list<int>, exceptional: list<int>}
     */
    public function getVariationIdsByTier(): array
    {
        return [
            'normal' => static::$normalVariationIds,
            'rare' => static::$rareVariationIds,
            'exceptional' => static::$exceptionalVariationIds,
        ];
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

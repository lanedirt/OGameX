<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Services\ObjectService;

class ExpeditionUnitsFound extends GameMessage
{
    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_units_found.body.1
     * - t_messages.expedition_units_found.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    private static int $numberOfVariations = 7;

    /**
     * The base key for the message.
     * @var string
     */
    private static string $baseKey = 'expedition_units_found';

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

        // Append the captured units to the body if the params are set.
        if (!empty($params)) {
            $translatedBody .= '<br /><br />' . __('t_messages.expedition_units_captured');

            // Get object service
            $objectService = app(ObjectService::class);

            // Append the units to the body by looping through all params that start with 'unit_'.
            foreach ($params as $key => $value) {
                if (str_starts_with($key, 'unit_')) {
                    // Get unit based on the unit id.
                    $unit_id = (int)str_replace('unit_', '', $key);
                    // Load gameobject based on the unit id.
                    $unit = $objectService->getUnitObjectById($unit_id);
                    $translatedBody .= '<br />' . $unit->title . ': ' . $value;
                }
            }
        }

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

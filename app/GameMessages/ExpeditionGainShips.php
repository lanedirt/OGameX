<?php

namespace OGame\GameMessages;

use OGame\Services\ObjectService;
use OGame\GameMessages\Abstracts\ExpeditionGameMessage;
use OGame\Facades\AppUtil;

class ExpeditionGainShips extends ExpeditionGameMessage
{
    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'expedition_gain_ships';

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_gain_ships.body.1
     * - t_messages.expedition_gain_ships.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations = 7;

    /**
     * Overides the body of the message to append the captured resource type and amount based on the params.
     */
    public function getBody(): string
    {
        // Change the body key to the correct random outcome message based on the params.
        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Get the base body.
        $translatedBody = parent::getBody();

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
                    $translatedBody .= '<br />' . $unit->title . ': ' . AppUtil::formatNumber((int)$value);
                }
            }
        }

        return $translatedBody;
    }
}

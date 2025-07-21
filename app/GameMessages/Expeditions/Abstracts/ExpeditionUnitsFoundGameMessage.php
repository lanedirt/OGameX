<?php

namespace OGame\GameMessages\Expeditions\Abstracts;

use OGame\Services\ObjectService;

abstract class ExpeditionUnitsFoundGameMessage extends ExpeditionGameMessage
{
    /**
     * Overides the body of the message to append the captured resource type and amount based on the params.
     */
    public function getBody(): string
    {
        $body = parent::getBody();

        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Append the captured units to the body if the params are set.
        if (!empty($params)) {
            $body .= '<br /><br />' . __('t_messages.expedition_units_captured');

            // Get object service
            $objectService = app(ObjectService::class);

            // Append the units to the body by looping through all params that start with 'unit_'.
            foreach ($params as $key => $value) {
                if (str_starts_with($key, 'unit_')) {
                    // Get unit based on the unit id.
                    $unit_id = (int)str_replace('unit_', '', $key);
                    // Load gameobject based on the unit id.
                    $unit = $objectService->getUnitObjectById($unit_id);
                    $body .= '<br />' . $unit->title . ': ' . $value;
                }
            }
        }

        return $body;
    }
}

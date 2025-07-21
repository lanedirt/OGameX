<?php

namespace OGame\GameMessages\Expeditions\Abstracts;

use OGame\Models\Enums\ResourceType;

abstract class ExpeditionResourcesFoundGameMessage extends ExpeditionGameMessage
{
    /**
     * Overides the body of the message to append the captured resource type and amount based on the params.
     */
    public function getBody(): string
    {
        $body = parent::getBody();

        $params = parent::checkParams($this->message->params);
        $params = parent::formatReservedParams($params);

        // Append the captured resource type and amount to the body if the params are set.
        if (!empty($params['resource_type']) && !empty($params['resource_amount'])) {
            // Convert resource type to human readable string.
            $resourceTypeLabel = ResourceType::from($params['resource_type'])->getTranslation();
            $body .= '<br /><br />' . __('t_messages.expedition_resources_captured', ['resource_type' => $resourceTypeLabel, 'resource_amount' => $params['resource_amount']]);
        }

        return $body;
    }
}

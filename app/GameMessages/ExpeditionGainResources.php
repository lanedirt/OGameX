<?php

namespace OGame\GameMessages;

use OGame\Models\Enums\ResourceType;
use OGame\GameMessages\Abstracts\ExpeditionGameMessage;
use OGame\Facades\AppUtil;

class ExpeditionGainResources extends ExpeditionGameMessage
{
    /**
     * The base key for the message.
     * @var string
     */
    protected static string $baseKey = 'expedition_gain_resources';

    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_gain_resources.body.1
     * - t_messages.expedition_gain_resources.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    protected static int $numberOfVariations = 6;

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

        // Append the captured resource type and amount to the body if the params are set.
        if (!empty($params['resource_type']) && !empty($params['resource_amount'])) {
            // Convert resource type to human readable string.
            $resourceTypeLabel = ResourceType::from($params['resource_type'])->getTranslation();
            $translatedBody .= '<br /><br />' . __('t_messages.expedition_resources_captured', ['resource_type' => $resourceTypeLabel, 'resource_amount' => AppUtil::formatNumber((int)$params['resource_amount'])]);
        }

        return $translatedBody;
    }
}

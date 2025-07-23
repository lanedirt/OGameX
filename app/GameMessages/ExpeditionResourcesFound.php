<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Enums\ResourceType;

class ExpeditionResourcesFound extends GameMessage
{
    /**
     * This controls the number of possible message variations. These should be added to the language files.
     * E.g. if this is 2, then the following message keys should be added to the language files:
     * - t_messages.expedition_resources_found.body.1
     * - t_messages.expedition_resources_found.body.2
     *
     * When increasing this number, make sure to add the english translations for the new message keys.
     *
     * @var int
     */
    private static int $numberOfVariations = 6;

    protected function initialize(): void
    {
        $this->key = 'expedition_resources_found';
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

        // If the message variation id param is set, use it to change the message to the expected variation.
        $this->key = 'expedition_resources_found_1';
        if (!empty($params['message_variation_id'])) {
            $params_message_key = 'expedition_resources_found_' . $params['message_variation_id'];

            // Check if the message key translation exists, if so, use it.
            if (__('t_messages.' . $params_message_key . '.body')) {
                $this->key = $params_message_key;
            }
        }

        // Get the body of the message via the normal method now that the correct variation message key is set.
        $body = parent::getBody();

        // Append the captured resource type and amount to the body if the params are set.
        if (!empty($params['resource_type']) && !empty($params['resource_amount'])) {
            // Convert resource type to human readable string.
            $resourceTypeLabel = ResourceType::from($params['resource_type'])->getTranslation();
            $body .= '<br /><br />' . __('t_messages.expedition_resources_captured', ['resource_type' => $resourceTypeLabel, 'resource_amount' => $params['resource_amount']]);
        }

        return $body;
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

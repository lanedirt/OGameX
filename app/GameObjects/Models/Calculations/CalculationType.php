<?php

namespace OGame\GameObjects\Models\Calculations;

/**
 * Enum CalculationType
 *
 * This enum represents the different types of calculations that are available to some objects.
 */
enum CalculationType: string
{
    case MAX_COLONIES = 'maxColonies';
    case MAX_EXPEDITION_SLOTS = 'maxExpeditionSlots';
    case MAX_FLEET_SLOTS = 'maxFleetSlots';
}

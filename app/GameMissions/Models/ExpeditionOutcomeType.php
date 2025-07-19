<?php

namespace OGame\GameMissions\Models;

/**
 * Expedition mission outcome types.
 */
enum ExpeditionOutcomeType: int
{
    /**
     * The expedition mission found resources.
     */
    case ResourcesFound = 1;

    /**
     * The expedition mission found dark matter.
     */
    case DarkMatterFound = 2;

    /**
     * The expedition mission found units.
     */
    case UnitsFound = 3;

    /**
     * The expedition mission found items.
     */
    case ItemsFound = 4;

    /**
     * The expedition mission failed.
     */
    case Failure = 5;

    /**
     * The expedition mission failed and the return trip was speeded up.
     */
    case FailureAndSpeedUp = 6;

    /**
     * The expedition mission failed and the return trip was delayed.
     */
    case FailureAndDelay = 7;

    /**
     * The expedition mission failed and the fleet was destroyed.
     */
    case FailureAndFleetDestroyed = 8;

    /**
     * The expedition mission encountered a hostile fleet and a battle ensued.
     */
    case Battle = 9;
}

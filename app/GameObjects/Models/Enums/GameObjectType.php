<?php

namespace OGame\GameObjects\Models\Enums;

/**
 * Enum that represents the type of game object.
 */
enum GameObjectType: string
{
    /**
     * Represents a building object (metal mine, crystal mine).
     */
    case Building = 'building';

    /**
     * Represents a station object (robotics factory, shipyard).
     */
    case Station = 'station';

    /**
     * Represents a ship (unit) object.
     */
    case Ship = 'ship';

    /**
     * Represents a defense (unit) object.
     */
    case Defense = 'defense';

    /**
     * Represents a research/technology object.
     */
    case Research = 'research';
}

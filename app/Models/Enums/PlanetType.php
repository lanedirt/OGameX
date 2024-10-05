<?php

namespace OGame\Models\Enums;

/**
 * Enum that represents the type of planet.
 */
enum PlanetType: int
{
    /**
     * Represents a planet.
     */
    case Planet = 1;

    /**
     * Represents a debris field.
     */
    case DebrisField = 2;

    /**
     * Represents a moon.
     */
    case Moon = 3;
}

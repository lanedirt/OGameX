<?php

namespace OGame\GameConstants;

/**
 * Constants related to universe structure and coordinate limits.
 */
final class UniverseConstants
{
    /**
     * Minimum galaxy number.
     */
    public const int MIN_GALAXY = 1;

    /**
     * Minimum system number per galaxy.
     */
    public const int MIN_SYSTEM = 1;

    /**
     * Minimum planet position per system.
     */
    public const int MIN_PLANET_POSITION = 1;

    /**
     * Maximum number of systems per galaxy.
     * Each galaxy contains systems numbered from MIN_SYSTEM to MAX_SYSTEM_COUNT.
     */
    public const int MAX_SYSTEM_COUNT = 499;

    /**
     * Maximum number of planet positions per system.
     * Planet positions are numbered from MIN_PLANET_POSITION to MAX_PLANET_POSITION.
     */
    public const int MAX_PLANET_POSITION = 15;

    /**
     * The expedition position in a system.
     * This is the special position where expedition missions are sent.
     * It's one position beyond the last planet position.
     */
    public const int EXPEDITION_POSITION = 16;
}

<?php

namespace OGame\Enums;

/**
 * Enum that represents the types of fleet speeds for different mission categories.
 */
enum FleetSpeedType: int
{
    case war = 1;
    case holding = 2;
    case peaceful = 3;
}

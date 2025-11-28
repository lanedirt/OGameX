<?php

namespace OGame\Enums;

/**
 * Enum that represents the friendly status of a fleet mission for UI styling.
 */
enum FleetMissionStatus: string
{
    case Friendly = 'friendly';
    case Neutral = 'neutral';
    case Hostile = 'hostile';
}

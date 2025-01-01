<?php

namespace OGame\Models\Enums;

/**
 * Enum that represents the type of resources.
 */
enum ResourceType: string
{
    case Metal = 'metal';

    case Crystal = 'crystal';

    case Deuterium = 'deuterium';
}

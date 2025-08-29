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

    /**
     * Get the translation of the resource type.
     * @return string
     */
    public function getTranslation(): string
    {
        return match ($this) {
            self::Metal => 'Metal',
            self::Crystal => 'Crystal',
            self::Deuterium => 'Deuterium',
        };
    }
}

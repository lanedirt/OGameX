<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

/**
 * Class CapacityPropertyService.
 *
 * @package OGame\Services
 */
class CapacityPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'capacity';

    /**
     * @inheritDoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        $hyperspace_technology_level = $player->getResearchLevel('hyperspace_technology');
        return 5 * $hyperspace_technology_level;
    }
}

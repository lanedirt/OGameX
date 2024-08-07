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
     * @inheritdoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        // TODO: implement capacity bonus calculation per object id.
        return 0;
    }
}

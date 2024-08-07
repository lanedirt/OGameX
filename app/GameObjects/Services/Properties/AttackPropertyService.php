<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

/**
 * Class AttackPropertyService.
 *
 * @package OGame\Services
 */
class AttackPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'attack';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        $weapons_technology_level = $player->getResearchLevel('weapon_technology');
        // Every level technology gives 10% bonus.
        return $weapons_technology_level * 10;
    }
}

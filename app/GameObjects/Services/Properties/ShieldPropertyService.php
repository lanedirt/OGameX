<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

/**
 * Class ShieldPropertyService.
 *
 * @package OGame\Services
 */
class ShieldPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'shield';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        $shielding_technology_level = $player->getResearchLevel('shielding_technology');
        // Every level technology gives 10% bonus.
        return $shielding_technology_level * 10;
    }
}

<?php

namespace OGame\Services\Objects\Properties;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class ObjectPropertyService.
 *
 * @package OGame\Services
 */
class SpeedPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'speed';

    /**
     * @inheritdoc
     */
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        parent::__construct($objects, $planet);
    }

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage($object_id): int
    {
        // Speed bonus is calculated based on main required drive technology.
        // Following technology gives amount of % per level:
        // 1. Combustion Drive: 10%
        // 2. Impulse Drive: 20%
        // 3. Hyperspace Drive: 30%

        // An object can have "speed_upgrade" defined which defines at what technology level
        // a drive switch is made. For example, if "speed_upgrade" is tech id. 118 and level 5
        // then the object will have 10% bonus per level from combustion drive until level 5
        // and 20% bonus per level from impulse drive after level 5.

        // Get object itself.
        $object = $this->objects->getObjects($object_id);

        // Get player's drive technology levels.
        $combustion_drive_level = $this->planet->getPlayer()->getResearchLevel(115);
        $impulse_drive_level = $this->planet->getPlayer()->getResearchLevel(117);
        $hyperspace_drive_level = $this->planet->getPlayer()->getResearchLevel(118);

        // Check if object has speed upgrade defined, if so, check if its eligible.
        $bonus_percentage_per_level = 0;
        $applicable_technology_level = 0;
        if (!empty($object['speed_upgrade'])) {
            foreach ($object['speed_upgrade'] as $technology_id => $speed_upgrade_from_level) {
                if ($technology_id === 115 && $combustion_drive_level >= $speed_upgrade_from_level) {
                    $bonus_percentage_per_level = 10;
                    $applicable_technology_level = $combustion_drive_level;
                } elseif ($technology_id === 117 && $impulse_drive_level >= $speed_upgrade_from_level) {
                    $bonus_percentage_per_level = 20;
                    $applicable_technology_level = $impulse_drive_level;
                } elseif ($technology_id === 118 && $hyperspace_drive_level >= $speed_upgrade_from_level) {
                    $bonus_percentage_per_level = 30;
                    $applicable_technology_level = $hyperspace_drive_level;
                }
            }
        }

        if ($bonus_percentage_per_level === 0) {
            // If no speed upgrade is defined, then check the main drive technology based on the required technologies.
            if (array_key_exists(115, $object['requirements'])) {
                // Combustion Drive is required.
                $bonus_percentage_per_level = 10;
                $applicable_technology_level = $combustion_drive_level;
            }
            else if (array_key_exists(117, $object['requirements'])) {
                // Impulse Drive is required.
                $bonus_percentage_per_level = 20;
                $applicable_technology_level = $impulse_drive_level;
            }
            else if (array_key_exists(118, $object['requirements'])) {
                // Hyperspace Drive is required.
                $bonus_percentage_per_level = 30;
                $applicable_technology_level = $hyperspace_drive_level;
            }
        }

        return $bonus_percentage_per_level * $applicable_technology_level;
    }
}

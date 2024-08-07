<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Models\Fields\GameObjectSpeedUpgrade;
use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

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
     * @throws \Exception
     */
    protected function getBonusPercentage(PlayerService $player): int
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
        $object = $this->parent_object;

        // Get player's drive technology levels.
        $combustion_drive_level = $player->getResearchLevel('combustion_drive');
        $impulse_drive_level = $player->getResearchLevel('impulse_drive');
        $hyperspace_drive_level = $player->getResearchLevel('hyperspace_drive');

        // Check if object has speed upgrade defined, if so, check if its eligible.
        $bonus_percentage_per_level = 0;
        $applicable_technology_level = 0;
        if (!empty($object->properties->speed_upgrade)) {
            /** @var GameObjectSpeedUpgrade $upgrade */
            foreach ($object->properties->speed_upgrade as $upgrade) {
                if ($upgrade->object_machine_name == 'combustion_drive' && $combustion_drive_level >= $upgrade->level) {
                    // If combustion drive is defined, then it will override the default speed bonus.
                    $bonus_percentage_per_level = 10;
                    $applicable_technology_level = $combustion_drive_level;
                } elseif ($upgrade->object_machine_name == 'impulse_drive' && $impulse_drive_level >= $upgrade->level) {
                    // If impulse drive is defined, then it will override combustion drive.
                    $bonus_percentage_per_level = 20;
                    $applicable_technology_level = $impulse_drive_level;
                } elseif ($upgrade->object_machine_name == 'hyperspace_drive' && $hyperspace_drive_level >= $upgrade->level) {
                    // If hyperspace drive is defined, then it will override impulse drive.
                    $bonus_percentage_per_level = 30;
                    $applicable_technology_level = $hyperspace_drive_level;
                }
            }
        }

        if ($bonus_percentage_per_level === 0) {
            // If no speed upgrade is defined, then check the main drive technology based on the required technologies.
            foreach ($object->requirements as $requirement) {
                if ($requirement->object_machine_name == 'combustion_drive') {
                    // Combustion drive gives 10% bonus per level.
                    $bonus_percentage_per_level = 10;
                    $applicable_technology_level = $combustion_drive_level;
                } elseif ($requirement->object_machine_name == 'impulse_drive') {
                    // Impulse drive gives 20% bonus per level.
                    $bonus_percentage_per_level = 20;
                    $applicable_technology_level = $impulse_drive_level;
                } elseif ($requirement->object_machine_name == 'hyperspace_drive') {
                    // Hyperspace drive gives 30% bonus per level.
                    $bonus_percentage_per_level = 30;
                    $applicable_technology_level = $hyperspace_drive_level;
                }
            }
        }

        return $bonus_percentage_per_level * $applicable_technology_level;
    }
}

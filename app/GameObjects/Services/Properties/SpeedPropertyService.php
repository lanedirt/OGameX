<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Models\Fields\GameObjectSpeedUpgrade;
use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

/**
 * Class SpeedPropertyService.
 *
 * Calculates the effective speed bonus for a ship, honoring drive “switch”
 * thresholds defined via $object->properties->speed_upgrade.
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
        // Base idea:
        // - If the ship defines speed_upgrade thresholds, use the best drive
        //   for which the player meets the required level (priority: hyperspace > impulse > combustion).
        // - Otherwise fall back to the ship’s base required drive.
        //
        // Rates per level:
        // - Combustion: 10%/lvl
        // - Impulse:    20%/lvl
        // - Hyperspace: 30%/lvl

        $object = $this->parent_object;

        // Player drive levels
        $levels = [
            'combustion_drive' => $player->getResearchLevel('combustion_drive'),
            'impulse_drive'    => $player->getResearchLevel('impulse_drive'),
            'hyperspace_drive' => $player->getResearchLevel('hyperspace_drive'),
        ];

        // Bonus rates per level
        $rate = [
            'combustion_drive' => 10,
            'impulse_drive'    => 20,
            'hyperspace_drive' => 30,
        ];

        // 1) Honor speed upgrades first (highest drive wins when threshold met)
        if (!empty($object->properties->speed_upgrade)) {
            // Build a quick lookup of required levels from the upgrade list
            $required = [];
            /** @var GameObjectSpeedUpgrade $u */
            foreach ($object->properties->speed_upgrade as $u) {
                $required[$u->object_machine_name] = $u->level;
            }

            // Check in order of best → worst drive so higher drives take precedence
            foreach (['hyperspace_drive', 'impulse_drive', 'combustion_drive'] as $drive) {
                if (isset($required[$drive]) && $levels[$drive] >= $required[$drive]) {
                    return $rate[$drive] * $levels[$drive];
                }
            }
            // If no upgrade threshold is met, we fall through to base requirements
        }

        // 2) Fallback: use the drive defined in the ship's base requirements
        if (!empty($object->requirements)) {
            foreach ($object->requirements as $requirement) {
                $drive = $requirement->object_machine_name;
                if (isset($rate[$drive])) {
                    return $rate[$drive] * $levels[$drive];
                }
            }
        }

        // No applicable drive
        return 0;
    }
}

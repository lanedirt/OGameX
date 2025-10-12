<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Models\Fields\GameObjectSpeedUpgrade;
use OGame\GameObjects\Models\Fields\GameObjectPropertyDetails;
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
     * Calculate the total value of the speed property, honoring both:
     * - drive bonus percentage (10/20/30% per level)
     * - base speed override at upgrade thresholds (e.g. SC @ Impulse 5 => 10,000)
     */
    public function calculateProperty(PlayerService $player): GameObjectPropertyDetails
    {
        $effectiveBase = $this->determineEffectiveBase($player);
        $bonusPercentage = $this->getBonusPercentage($player);

        $bonusValue = (($effectiveBase / 100) * $bonusPercentage);
        $totalValue = $effectiveBase + $bonusValue;

        $breakdown = [
            'rawValue' => $effectiveBase,
            'bonuses' => [
                [
                    'type' => 'Research bonus',
                    'value' => $bonusValue,
                    'percentage' => $bonusPercentage,
                ],
            ],
            'totalValue' => $totalValue,
        ];

        return new GameObjectPropertyDetails($effectiveBase, $bonusValue, $totalValue, $breakdown);
    }

    /**
     * Determine base speed override based on speed_upgrade thresholds.
     * Higher-index upgrades take precedence (last match wins).
     */
    private function determineEffectiveBase(PlayerService $player): int
    {
        $object = $this->parent_object;
        $effectiveBase = $this->base_value;

        if (!empty($object->properties->speed_upgrade)) {
            foreach ($object->properties->speed_upgrade as $upgrade) {
                if (!($upgrade instanceof GameObjectSpeedUpgrade)) {
                    continue;
                }

                $meetsThreshold = match ($upgrade->object_machine_name) {
                    'combustion_drive'  => $player->getResearchLevel('combustion_drive')  >= $upgrade->level,
                    'impulse_drive'     => $player->getResearchLevel('impulse_drive')     >= $upgrade->level,
                    'hyperspace_drive'  => $player->getResearchLevel('hyperspace_drive')  >= $upgrade->level,
                    default => false,
                };

                if ($meetsThreshold && $upgrade->base_speed !== null) {
                    // Last applicable upgrade wins (matches "higher index takes precedence")
                    $effectiveBase = (int)$upgrade->base_speed;
                }
            }
        }

        return $effectiveBase;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        // Speed bonus is calculated based on drive technology:
        // Combustion: 10%/lvl, Impulse: 20%/lvl, Hyperspace: 30%/lvl

        $object = $this->parent_object;

        // Drive levels
        $combustion_drive_level = $player->getResearchLevel('combustion_drive');
        $impulse_drive_level = $player->getResearchLevel('impulse_drive');
        $hyperspace_drive_level = $player->getResearchLevel('hyperspace_drive');

        // Allow speed_upgrade to override which drive applies (last match wins)
        $bonus_percentage_per_level = 0;
        $applicable_technology_level = 0;

        if (!empty($object->properties->speed_upgrade)) {
            /** @var GameObjectSpeedUpgrade $upgrade */
            foreach ($object->properties->speed_upgrade as $upgrade) {
                if ($upgrade->object_machine_name == 'combustion_drive' && $combustion_drive_level >= $upgrade->level) {
                    $bonus_percentage_per_level = 10;
                    $applicable_technology_level = $combustion_drive_level;
                } elseif ($upgrade->object_machine_name == 'impulse_drive' && $impulse_drive_level >= $upgrade->level) {
                    $bonus_percentage_per_level = 20;
                    $applicable_technology_level = $impulse_drive_level;
                } elseif ($upgrade->object_machine_name == 'hyperspace_drive' && $hyperspace_drive_level >= $upgrade->level) {
                    $bonus_percentage_per_level = 30;
                    $applicable_technology_level = $hyperspace_drive_level;
                }
            }
        }

        if ($bonus_percentage_per_level === 0) {
            // Fall back to the required drive on the object itself
            foreach ($object->requirements as $requirement) {
                if ($requirement->object_machine_name == 'combustion_drive') {
                    $bonus_percentage_per_level = 10;
                    $applicable_technology_level = $combustion_drive_level;
                } elseif ($requirement->object_machine_name == 'impulse_drive') {
                    $bonus_percentage_per_level = 20;
                    $applicable_technology_level = $impulse_drive_level;
                } elseif ($requirement->object_machine_name == 'hyperspace_drive') {
                    $bonus_percentage_per_level = 30;
                    $applicable_technology_level = $hyperspace_drive_level;
                }
            }
        }

        return $bonus_percentage_per_level * $applicable_technology_level;
    }
}

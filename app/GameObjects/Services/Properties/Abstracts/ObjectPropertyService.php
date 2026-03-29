<?php

namespace OGame\GameObjects\Services\Properties\Abstracts;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Fields\GameObjectPropertyDetails;
use OGame\Services\PlayerService;

/**
 * Class ObjectPropertyService.
 *
 * @package OGame\Services
 */
abstract class ObjectPropertyService
{
    /**
     * This is a placeholder for the property name set by the child class.
     *
     * @var string
     */
    protected string $propertyName = '';

    /**
     * Registry of module-provided bonus modifier callables, keyed by property name.
     * Each callable receives (PlayerService $player, GameObject $object) and returns int percentage.
     * Applied to base_value, additive alongside the research bonus.
     *
     * @var array<string, array<callable>>
     */
    private static array $bonusModifiers = [];

    public function __construct(protected GameObject $parent_object, protected int $base_value)
    {
    }

    /**
     * Register an additional bonus percentage for a named property.
     *
     * Usage in a module's bootModule():
     *   ObjectPropertyService::registerBonusModifier('attack', function (PlayerService $player, GameObject $object): int {
     *       return $player->getResearchLevel('my_tech') * 5;
     *   });
     *
     * @param string   $property The property name: 'attack', 'shield', 'structural_integrity', 'capacity', 'fuel', etc.
     * @param callable $fn       Receives (PlayerService, GameObject), returns int percentage applied to base_value
     */
    public static function registerBonusModifier(string $property, callable $fn): void
    {
        self::$bonusModifiers[$property][] = $fn;
    }

    /**
     * Get the bonus percentage for a property.
     *
     * @return int
     *  Bonus percentage as integer (e.g. 10 for 10% bonus, 110 for 110% bonus, etc.)
     */
    abstract protected function getBonusPercentage(PlayerService $player): int;

    /**
     * Calculate the total value of a property.
     *
     * @param PlayerService $player
     * @return GameObjectPropertyDetails
     */
    public function calculateProperty(PlayerService $player): GameObjectPropertyDetails
    {
        // Research bonus applied to base_value
        $researchBonus = $this->getBonusPercentage($player);
        $researchBonusValue = intdiv($this->base_value * $researchBonus, 100);

        // Module bonus modifiers — each returns an int percentage applied to base_value,
        // additive alongside the research bonus (same pattern as character class bonuses).
        $moduleBonus = 0;
        $moduleBreakdown = [];
        foreach (self::$bonusModifiers[$this->propertyName] ?? [] as $fn) {
            $pct = $fn($player, $this->parent_object);
            if ($pct !== 0) {
                $moduleBonus += $pct;
                $moduleBreakdown[] = [
                    'type'       => 'Module bonus',
                    'value'      => intdiv($this->base_value * $pct, 100),
                    'percentage' => $pct,
                ];
            }
        }
        $moduleBonusValue = intdiv($this->base_value * $moduleBonus, 100);

        $totalValue = $this->base_value + $researchBonusValue + $moduleBonusValue;

        $bonuses = [];
        if ($researchBonus > 0) {
            $bonuses[] = [
                'type'       => 'Research bonus',
                'value'      => $researchBonusValue,
                'percentage' => $researchBonus,
            ];
        }
        foreach ($moduleBreakdown as $entry) {
            $bonuses[] = $entry;
        }

        $breakdown = [
            'rawValue'   => $this->base_value,
            'bonuses'    => $bonuses,
            'totalValue' => $totalValue,
        ];

        return new GameObjectPropertyDetails($this->base_value, $researchBonusValue + $moduleBonusValue, $totalValue, $breakdown);
    }
}

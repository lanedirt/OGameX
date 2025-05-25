<?php

namespace OGame\GameObjects\Models\Abstracts;

use InvalidArgumentException;
use OGame\GameObjects\Models\Calculations\CalculationType;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectProduction;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\Models\Enums\PlanetType;

/**
 * Class GameObject
 *
 * @package OGame\GameObjects\Models
 *
 * The GameObject class is the base class for all game objects like buildings, units, researches, etc.
 */
abstract class GameObject
{
    public int $id;
    public string $title;
    public GameObjectType $type;
    public string $machine_name;

    /**
     * Optional class name of the object used in frontend which differs from the machine name.
     *
     * @var string
     */
    public string $class_name = '';

    public string $description;
    public string $description_long;

    /**
     * Objects that this object requires on with required level.
     *
     * @var array<GameObjectRequirement>
     */
    public array $requirements = [];

    /**
     * Array of planet types that this object can be built on. Empty array means it can be built on any planet type.
     *
     * @var array<PlanetType>
     */
    public array $valid_planet_types = [];

    /**
     * Production gained by object (in case of mines, solar satellites, plasma technology, etc).
     */
    public GameObjectProduction $production;

    /**
     * Price of the object.
     *
     * @var GameObjectPrice
     */
    public GameObjectPrice $price;

    /**
     * Assets of the object.
     *
     * @var GameObjectAssets
     */
    public GameObjectAssets $assets;

    /**
     * Custom calculation formulas for this object. These formulas can be used to calculate custom values for the object.
     * E.g. a formula to determine max amount of planets that can be colonized with astrophysics technology.
     *
     * @var array<string, callable>
     */
    protected array $calculations = [];

    /**
     * Add a custom calculation method to the object which can be used to define custom calculation formulas.
     * E.g. a formula to determine max amount of planets that can be colonized with astrophysics technology.
     *
     * @param CalculationType $calculationName
     * @param callable $method The method that performs the calculation and returns the result as an integer.
     * @return void
     */
    public function addCalculation(CalculationType $calculationName, callable $method): void
    {
        $this->calculations[$calculationName->value] = $method;
    }

    /**
     * Perform a custom calculation on the object.
     *
     * Note: the requested calculation has to be added to the specific object before via the addCalculation method.
     * For example see the usage of the addCalculation method for the Astrophysics research object definition.
     *
     * @param CalculationType $calculationName
     * @param mixed ...$args
     * @return int
     */
    public function performCalculation(CalculationType $calculationName, ...$args): int
    {
        if (isset($this->calculations[$calculationName->value])) {
            return $this->calculations[$calculationName->value](...$args);
        }
        throw new InvalidArgumentException("Calculation method '$calculationName->value' not found.");
    }

    /**
     * Check if the object has any requirements.
     *
     * @return bool
     */
    public function hasRequirements(): bool
    {
        return count($this->requirements) > 0;
    }
}

<?php

namespace OGame\Services\Objects\Properties\Abstracts;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Models\ObjectPropertyDetails;
use OGame\Services\PlanetService;
use voku\helper\ASCII;

/**
 * Class ObjectPropertyService.
 *
 * @package OGame\Services
 */
abstract class ObjectPropertyService
{
    protected ObjectService $objects;

    protected PlanetService $planet;

    /**
     * This is a placeholder for the property name set by the child class.
     *
     * @var string
     */
    protected string $propertyName = '';

    /**
     * ObjectPropertyService constructor.
     */
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        $this->objects = $objects;
        $this->planet = $planet;
    }

    /**
     * Get the raw value of a property.
     *
     * @param int $object_id
     * @return mixed
     */
    protected function getRawValue(int $object_id) {
        // Check if the property exists in the object
        if (!array_key_exists($this->propertyName, $this->objects->getObjects($object_id)['properties'])) {
            return 0;
        }

        return $this->objects->getObjects($object_id)['properties'][$this->propertyName];
    }

    /**
     * Get the bonus percentage for a property.
     *
     * @param int $object_id
     * @return int
     *  Bonus percentage as integer (e.g. 10 for 10% bonus, 110 for 110% bonus, etc.)
     */
    abstract protected function getBonusPercentage(int $object_id): int;

    /**
     * Calculate the total value of a property.
     *
     * @param int $object_id
     * @return ObjectPropertyDetails
     */
    public function calculateProperty(int $object_id): ObjectPropertyDetails
    {
        $rawValue = $this->getRawValue($object_id);
        $bonusPercentage = $this->getBonusPercentage($object_id);
        $bonusValue = (($rawValue / 100) * $bonusPercentage);

        $totalValue = $rawValue + $bonusValue;

        // Prepare the breakdown for future-proofing (assuming more components might be added)
        // TODO: Add more components to the breakdown if necessary like class bonuses, premium member
        // bonuses, item bonuses etc.
        $breakdown = [
            'rawValue' => $rawValue,
            'bonuses' => [
                [
                    'type' => 'Research bonus',
                    'value' => $bonusValue,
                    'percentage' => $bonusPercentage,
                ],
            ],
            'totalValue' => $totalValue,
        ];

        return new ObjectPropertyDetails($rawValue, $bonusValue, $totalValue, $breakdown);
    }
}

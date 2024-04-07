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
     * @param int $objectId
     * @return mixed
     */
    protected function getRawValue(int $object_id) {
        return $this->objects->getObjects($object_id)['properties'][$this->propertyName];
    }

    /**
     * Get the bonus percentage for a property.
     *
     * @param $objectId
     * @return int
     *  Bonus percentage as integer (e.g. 10 for 10% bonus, 110 for 110% bonus, etc.)
     */
    abstract protected function getBonusPercentage($object_id): int;

    /**
     * Calculate the total value of a property.
     *
     * @param int $object_id
     * @param string $property_name
     * @return ObjectPropertyDetails
     */
    public function calculateProperty(int $object_id): ObjectPropertyDetails
    {
        $rawValue = $this->getRawValue($object_id);
        $bonusPercentage = $this->getBonusPercentage($object_id);
        $bonusValue = (($rawValue / 100) * $bonusPercentage);

        $totalValue = $rawValue + $bonusValue;

        // Prepare the breakdown for future-proofing (assuming more components might be added)
        $breakdown = [
            'rawValue' => $rawValue,
            'bonuses' => [
                [
                    'type' => 'research',
                    'value' => $bonusValue,
                    'percentage' => $bonusPercentage,
                ],
            ],
            'totalValue' => $totalValue,
        ];

        return new ObjectPropertyDetails($rawValue, $bonusValue, $totalValue, $breakdown);
    }
}

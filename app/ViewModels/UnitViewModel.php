<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Models\Resource;
use OGame\Services\PlanetService;

class UnitViewModel
{
    public int $count;
    public GameObject $object;
    public int $amount;
    public bool $requirements_met;
    public bool $enough_resources;
    public int $max_build_amount;
    public bool $currently_building;
    public int $currently_building_amount;

    /**
     * Wrap the amount inside a Resource instance and return it.
     */
    protected function getResource(): Resource
    {
        return new Resource($this->amount);
    }

    public function getFormatted(): string
    {
        return $this->getResource()->getFormatted();
    }

    public function getFormattedFull(): string
    {
        return $this->getResource()->getFormattedFull();
    }

    public function getFormattedLong(): string
    {
        return $this->getResource()->getFormattedLong();
    }

    /**
     * Get the description with dynamic values replaced based on planet context.
     * This method handles special cases like Solar Satellite where the description
     * needs to show planet-specific energy production values.
     *
     * @param PlanetService $planet
     * @return string
     */
    public function getDescription(PlanetService $planet): string
    {
        // Return the original description without modifications
        // Dynamic descriptions should only be shown in specific contexts (like AJAX object details)
        return $this->object->description;
    }
}

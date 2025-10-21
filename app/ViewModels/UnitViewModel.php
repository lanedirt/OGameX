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
        $description = $this->object->description;
        
        // Special handling for Solar Satellite to show correct energy production
        if ($this->object->machine_name === 'solar_satellite') {
            // Calculate energy per unit using the same formula as in production
            $energyPerUnit = floor(($planet->getPlanetTempMax() + 140) / 6);
            
            // Replace any occurrence of "produces [number] energy" with the calculated value
            $description = preg_replace(
                '/produces \d+ energy/',
                "produces {$energyPerUnit} energy",
                $description
            );
        }
        
        return $description;
    }
}

<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Models\Resource;

class UnitViewModel
{
    public int $count;
    public GameObject $object;
    public int $amount;
    public bool $requirements_met;
    public bool $character_class_met;
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
}

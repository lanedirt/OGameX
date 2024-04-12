<?php

namespace OGame\Models;

class Resources
{
    public Resource $metal;
    public Resource $crystal;
    public Resource $deuterium;
    public Resource $energy;

    public function __construct(int $metal, int $crystal, int $deuterium, int $energy)
    {
        $this->metal = new Resource($metal);
        $this->crystal = new Resource($crystal);
        $this->deuterium = new Resource($deuterium);
        $this->energy = new Resource($energy);
    }

    /**
     * Add another resources to this one.
     *
     * @param Resources $other
     * @return void
     */
    public function add(Resources $other): void {
        $this->metal->add($other->metal);
        $this->crystal->add($other->crystal);
        $this->deuterium->add($other->deuterium);
        $this->energy->add($other->energy);
    }
}
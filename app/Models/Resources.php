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
}
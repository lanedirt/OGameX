<?php

namespace OGame\Models;

class Resources
{
    public Resource $metal;
    public Resource $crystal;
    public Resource $deuterium;
    public Resource $energy;

    public function __construct(int|float $metal = 0, int|float $crystal = 0, int|float $deuterium = 0, int|float $energy = 0)
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
    public function add(Resources $other): void
    {
        $this->metal->add($other->metal);
        $this->crystal->add($other->crystal);
        $this->deuterium->add($other->deuterium);
        $this->energy->add($other->energy);
    }

    /**
     * Returns sum of all resources.
     *
     * @return float
     */
    public function sum(): float
    {
        return $this->metal->get() + $this->crystal->get() + $this->deuterium->get() + $this->energy->get();
    }

    /**
     * Returns true if any resource is greater than 0.
     *
     * @return bool
     */
    public function any(): bool
    {
        return $this->metal->get() > 0 || $this->crystal->get() > 0 || $this->deuterium->get() > 0 || $this->energy->get() > 0;
    }

    /**
     * Multiply all resources by a factor.
     *
     * @param float $factor
     * @return Resources
     */
    public function multiply(float $factor): Resources
    {
        $this->metal->multiply($factor);
        $this->crystal->multiply($factor);
        $this->deuterium->multiply($factor);
        $this->energy->multiply($factor);

        return $this;
    }
}

<?php

namespace OGame\Models\Planet;

/**
 * Represents a coordinate in the universe.
 */
class Coordinate
{
    public function __construct(public int $galaxy, public int $system, public int $position)
    {
    }

    /**
     * Returns the coordinate as a string.
     *
     * @return string
     */
    public function asString(): string
    {
        return $this->galaxy . ':' . $this->system . ':' . $this->position;
    }

    /**
     * Returns true if the coordinate is equal to another coordinate.
     *
     * @param Coordinate $coordinate
     * @return bool
     */
    public function equals(Coordinate $coordinate): bool
    {
        return $this->galaxy === $coordinate->galaxy && $this->system === $coordinate->system && $this->position === $coordinate->position;
    }
}

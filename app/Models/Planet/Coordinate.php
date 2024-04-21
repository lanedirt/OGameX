<?php

namespace OGame\Models\Planet;

/**
 * Represents a coordinate in the universe.
 */
class Coordinate {
    public int $galaxy;
    public int $system;
    public int $position;

    public function __construct(int $galaxy, int $system, int $position) {
        $this->galaxy = $galaxy;
        $this->system = $system;
        $this->position = $position;
    }

    /**
     * Returns the coordinate as a string.
     *
     * @return string
     */
    public function asString() : string {
        return $this->galaxy . ':' . $this->system . ':' . $this->position;
    }
}
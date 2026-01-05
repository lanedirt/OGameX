<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectSpeedUpgrade
{
    public function __construct(
        /**
         * Research object that is required for speed upgrade.
         */
        public string $object_machine_name,
        /**
         * Required level of the research object for speed upgrade.
         */
        public int $level,
        /**
         * Optional override for the ship's base speed once this upgrade applies.
         * If null, the object's original base speed is used.
         */
        public int|null $base_speed = null
    )
    {
    }
}

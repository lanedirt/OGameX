<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectSpeedUpgrade
{
    /**
     * GameObjectSpeedUpgrade constructor.
     * @param string $object_machine_name Research object that is required for speed upgrade.
     * @param int $level Required level of the research object for speed upgrade.
     * @param int|null $base_speed Optional override for the ship's base speed once this upgrade applies.
     */
    public function __construct(public string $object_machine_name, public int $level, public int|null $base_speed = null)
    {
    }
}

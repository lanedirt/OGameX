<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectSpeedUpgrade
{
    /**
     * Research object that is required for speed upgrade.
     *
     * @var string
     */
    public string $object_machine_name;

    /**
     * Required level of the research object for speed upgrade.
     *
     * @var int
     */
    public int $level;

    public function __construct(string $object_machine_name, int $level)
    {
        $this->object_machine_name = $object_machine_name;
        $this->level = $level;
    }
}

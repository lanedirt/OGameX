<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectRequirement
{
    public string $object_machine_name;
    public int $level;

    public function __construct(string $object_machine_name, int $level)
    {
        $this->object_machine_name = $object_machine_name;
        $this->level = $level;
    }
}

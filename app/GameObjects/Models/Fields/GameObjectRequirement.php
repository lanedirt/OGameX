<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectRequirement
{
    public function __construct(public string $object_machine_name, public int $level)
    {
    }
}

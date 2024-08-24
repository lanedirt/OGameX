<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Enums\GameObjectType;

class ResearchObject extends GameObject
{
    public GameObjectType $type = GameObjectType::Research;
}

<?php

namespace OGame\Services\Objects\Models\Fields;

use OGame\Services\Objects\Models\ResearchObject;

class GameObjectSpeedUpgrade
{
    /**
     * Research object that is required for speed upgrade.
     *
     * @var ResearchObject
     */
    public ResearchObject $object;

    /**
     * Required level of the research object for speed upgrade.
     *
     * @var int
     */
    public int $level;
}
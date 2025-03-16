<?php

namespace OGame\GameObjects\Models\Techtree;

use OGame\GameObjects\Models\Abstracts\GameObject;

/**
 * Class TechtreeRequirement
 *
 * Represents requirement that current object requires on. Used to render the tech tree graph.
 *
 * @package OGame\GameObjects\Models
 */
class TechtreeRequirement
{
    /**
     * @var GameObject The GameObject that is required.
     */
    public GameObject $gameObject;

    /**
     * @var int The level that is required by the parent object.
     */
    public int $level_required;

    /**
     * @var int The level that this current object is at.
     */
    public int $level_current;

    /**
     * GameObjectRequirement constructor.
     *
     * @param GameObject $gameObject
     * @param int $levelRequired
     * @param int $levelCurrent
     */
    public function __construct(GameObject $gameObject, int $levelRequired, int $levelCurrent)
    {
        $this->gameObject = $gameObject;
        $this->level_required = $levelRequired;
        $this->level_current = $levelCurrent;
    }
}

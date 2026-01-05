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
     * GameObjectRequirement constructor.
     *
     * @param int $depth
     * @param int $column
     * @param TechtreeRequirement|null $parent
     * @param GameObject $gameObject
     * @param int $levelRequired
     * @param int $levelCurrent
     */
    public function __construct(public int $depth, public int $column, public TechtreeRequirement|null $parent, public GameObject $gameObject, public int $levelRequired, public int $levelCurrent)
    {
    }
}

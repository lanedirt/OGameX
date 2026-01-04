<?php

namespace OGame\GameObjects\Models\Techtree;

use OGame\GameObjects\Models\Abstracts\GameObject;

/**
 * Class TechtreeRequiredBy
 *
 * Represents a requirement for a GameObject. Used to render applications GUI.
 *
 * @package OGame\GameObjects\Models
 */
class TechtreeRequiredBy
{
    /**
     * GameObjectRequirement constructor.
     *
     * @param GameObject $gameObject
     * @param bool $requirementsMet
     */
    public function __construct(public GameObject $gameObject, public bool $requirementsMet)
    {
    }
}

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
     * @var GameObject The GameObject that is required.
     */
    public GameObject $gameObject;

    /**
     * @var bool Whether the requirements are met.
     */
    public bool $requirementsMet;

    /**
     * GameObjectRequirement constructor.
     *
     * @param GameObject $gameObject
     * @param bool $requirementsMet
     */
    public function __construct(GameObject $gameObject, bool $requirementsMet)
    {
        $this->gameObject = $gameObject;
        $this->requirementsMet = $requirementsMet;
    }
}

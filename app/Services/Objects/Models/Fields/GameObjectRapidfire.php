<?php

namespace OGame\Services\Objects\Models\Fields;

use OGame\Services\Objects\Models\GameObject;

class GameObjectRapidfire
{
    /**
     * Unit that this rapidfire affects.
     *
     * @var GameObject
     */
    public GameObject $object;

    /**
     * Chance of rapidfire.
     *
     * @var int
     */
    public int $chance;

    /**
     * Amount of rapidfire.
     *
     * @var int
     */
    public int $amount;
}
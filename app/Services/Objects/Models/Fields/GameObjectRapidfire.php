<?php

namespace OGame\Services\Objects\Models\Fields;

class GameObjectRapidfire
{
    /**
     * Unit that this rapidfire affects.
     *
     * @var string
     */
    public string $object_machine_name;

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

    /**
     * GameObjectRapidfire constructor.
     *
     * @param string $object_machine_name
     * @param int $chance
     * @param int $amount
     */
    public function __construct(string $object_machine_name, int $chance, int $amount)
    {
        $this->object_machine_name = $object_machine_name;
        $this->chance = $chance;
        $this->amount = $amount;
    }
}
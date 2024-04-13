<?php

namespace OGame\GameObjects\Models\Fields;

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
     * @var float
     */
    public float $chance;

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
     * @param float $chance
     * @param int $amount
     */
    public function __construct(string $object_machine_name, float $chance, int $amount)
    {
        $this->object_machine_name = $object_machine_name;
        $this->chance = $chance;
        $this->amount = $amount;
    }
}
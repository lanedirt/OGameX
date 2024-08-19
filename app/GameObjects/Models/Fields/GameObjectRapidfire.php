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
     * @return float
     */
    public function getChance(): float
    {
        // If value is already calculated, return it.
        if ($this->chance > 0) {
            return $this->chance;
        }

        // If value is not calculated, calculate it and store it in local object.
        // Rapidfire chance is calculated as 100 - (100 / amount). For example:
        // - rapidfire amount of 4 means 100 - (100 / 4) = 75% chance.
        // - rapidfire amount of 10 means 100 - (100 / 10) = 90% chance.
        // - rapidfire amount of 33 means 100 - (100 / 33) = 96.97%
        $chance = 100 / $this->amount;
        // Round down to 2 decimal places. E.g. 0.336 -> 0.33
        $rounded_chance = floor($chance * 100) / 100;
        $this->chance = 100 - $rounded_chance;

        return $this->chance;
    }

    private float $chance = 0;

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
     * @param int $amount
     */
    public function __construct(string $object_machine_name, int $amount)
    {
        $this->object_machine_name = $object_machine_name;
        $this->amount = $amount;
    }
}

<?php

namespace OGame\Services\Objects\Models\Fields;

class GameObjectProperties
{
    public int $structural_integrity;
    public int $shield;
    public int $attack;
    public int $speed;
    public int $capacity;
    public int $fuel;

    /**
     * Upgrades to speed for this object depending on alternative drive technology level. Items with higher
     * index take precedence over items with lower index.
     *
     * @var array<GameObjectSpeedUpgrade>
     */
    public array $speed_upgrade;
}
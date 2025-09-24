<?php

namespace OGame\Models;

class ProductionIndex
{
    // basic income
    public Resources $basic;

    // mine only, includes solar satellite
    public Resources $mine;

    // basic + mine + all bonuses
    public Resources $total;

    // plasma technology bonus amount
    public Resources $plasma_technology;

    // planet slot bonus amount
    public Resources $planet_slot;

    // Engineer bonus amount, for energy
    public Resources $engineer;

    // Geologist bonus amount
    public Resources $geologist;

    // Commanding Staff bonus amount
    public Resources $commanding_staff;

    public function __construct()
    {
        $this->basic = new Resources();
        $this->mine = new Resources();
        $this->total = new Resources();
        $this->plasma_technology = new Resources();
        $this->planet_slot = new Resources();
        $this->engineer = new Resources();
        $this->geologist = new Resources();
        $this->commanding_staff = new Resources();
    }

    public function add(ProductionIndex $productionIndex): void
    {
        $this->basic->add($productionIndex->basic);
        $this->mine->add($productionIndex->mine);
        $this->total->add($productionIndex->total);
        $this->plasma_technology->add($productionIndex->plasma_technology);
        $this->planet_slot->add($productionIndex->planet_slot);
        $this->engineer->add($productionIndex->engineer);
        $this->geologist->add($productionIndex->geologist);
        $this->commanding_staff->add($productionIndex->commanding_staff);
    }
}

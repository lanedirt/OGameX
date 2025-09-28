<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\Models\{
    ProductionIndex,
};
use OGame\Services\{
    PlanetService,
    PlayerService,
};

class GameObjectProduction
{
    // fn(GameObjectProduction, int $level): float
    public ?\Closure $metal_formula = null;
    public ?\Closure $crystal_formula = null;
    public ?\Closure $deuterium_formula = null;
    public ?\Closure $energy_formula = null;

    public PlanetService $planetService;
    public PlayerService $playerService;

    public int $universe_speed = 1;
    public int $plasma_technology_level = 0;

    public function calculate(int $level, float $building_percentage = 1): ProductionIndex
    {
        $productionIndex = new ProductionIndex();

        if (empty($this->planetService) || empty($this->playerService)) {
            return $productionIndex;
        }

        $this->calculateMine($productionIndex, $level, $building_percentage);
        // planet slot bonus is counted toward the mine's income
        //    all other multiplier (plasma, officers) applies on top of planet slot bonus
        //    therefore, this value needs to be calculated right before other bonuses
        $this->calculatePlanetSlot($productionIndex);
        $this->calculatePlasmaTech($productionIndex, $level, $building_percentage);
        $this->calculateEngineer($productionIndex);
        $this->calculateGeologist($productionIndex);
        $this->calculateCommandingStaff($productionIndex);
        $this->calculateTotal($productionIndex);

        return $productionIndex;
    }

    private function calculateMine(ProductionIndex $productionIndex, int $level, float $building_percentage = 1): void
    {
        if ($this->metal_formula) {
            $productionIndex->mine->metal->set(
                $this->metal_formula->__invoke($this, $level)
                * $building_percentage
                * $this->universe_speed
            );
        }

        if ($this->crystal_formula) {
            $productionIndex->mine->crystal->set(
                $this->crystal_formula->__invoke($this, $level)
                * $building_percentage
                * $this->universe_speed
            );
        }

        if ($this->deuterium_formula) {
            $productionIndex->mine->deuterium->set(
                $this->deuterium_formula->__invoke($this, $level)
                * $building_percentage
                * $this->universe_speed
            );
        }

        if ($this->energy_formula) {
            $productionIndex->mine->energy->set(
                $this->energy_formula->__invoke($this, $level)
                * $building_percentage
            );
        }
    }

    private function calculatePlasmaTech(ProductionIndex $productionIndex, int $level, float $building_percentage = 1): void
    {
        $this->plasma_technology_level = $this->playerService->getResearchLevel('plasma_technology');

        if ($this->metal_formula && $productionIndex->mine->metal->get() > 0) {
            $productionIndex->plasma_technology->metal->set(
                floor(
                    ($productionIndex->mine->metal->get() + $productionIndex->planet_slot->metal->get())
                    * 0.01 * $this->plasma_technology_level
                )
            );
        }

        if ($this->crystal_formula && $productionIndex->mine->crystal->get() > 0) {
            $productionIndex->plasma_technology->crystal->set(
                floor(
                    ($productionIndex->mine->crystal->get() + $productionIndex->planet_slot->crystal->get())
                    * 0.0066 * $this->plasma_technology_level
                )
            );
        }

        if ($this->deuterium_formula && $productionIndex->mine->deuterium->get() > 0) {
            $productionIndex->plasma_technology->deuterium->set(
                floor(
                    ($productionIndex->mine->deuterium->get() + $productionIndex->planet_slot->deuterium->get())
                    * 0.0033 * $this->plasma_technology_level
                )
            );
        }
    }

    private function calculatePlanetSlot(ProductionIndex $productionIndex): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();
        $bonus = $this->planetService->getProductionForPositionBonuses($coordinates->position);

        $productionIndex->planet_slot->metal->set(
            floor($productionIndex->mine->metal->get() * ($bonus['metal'] - 1))
        );

        $productionIndex->planet_slot->crystal->set(
            floor($productionIndex->mine->crystal->get() * ($bonus['crystal'] - 1))
        );

        $productionIndex->planet_slot->deuterium->set(
            floor($productionIndex->mine->deuterium->get() * ($bonus['deuterium'] - 1))
        );
    }

    private function calculateEngineer(ProductionIndex $productionIndex): void
    {
        if (!$this->playerService->hasEngineer()) {
            return;
        }

        // do not apply bonus to consumption
        if ($productionIndex->mine->energy->get() > 0) {
            $productionIndex->engineer->energy->set(
                floor($productionIndex->mine->energy->get() * 0.1)
            );
        }
    }

    private function calculateGeologist(ProductionIndex $productionIndex): void
    {
        if (!$this->playerService->hasGeologist()) {
            return;
        }

        if ($productionIndex->mine->metal->get() > 0) {
            $productionIndex->geologist->metal->set(
                floor(
                    ($productionIndex->mine->metal->get() + $productionIndex->planet_slot->metal->get())
                    * 0.1
                )
            );
        }

        if ($productionIndex->mine->crystal->get() > 0) {
            $productionIndex->geologist->crystal->set(
                floor(
                    ($productionIndex->mine->crystal->get() + $productionIndex->planet_slot->crystal->get())
                    * 0.1
                )
            );
        }

        if ($productionIndex->mine->deuterium->get() > 0) {
            $productionIndex->geologist->deuterium->set(
                floor(
                    ($productionIndex->mine->deuterium->get() + $productionIndex->planet_slot->deuterium->get())
                    * 0.1
                )
            );
        }
    }

    private function calculateCommandingStaff(ProductionIndex $productionIndex): void
    {
        if (!$this->playerService->hasCommandingStaff()) {
            return;
        }

        if ($productionIndex->mine->metal->get() > 0) {
            $productionIndex->commanding_staff->metal->set(
                floor(
                    ($productionIndex->mine->metal->get() + $productionIndex->planet_slot->metal->get())
                    * 0.02
                )
            );
        }

        if ($productionIndex->mine->crystal->get() > 0) {
            $productionIndex->commanding_staff->crystal->set(
                floor(
                    ($productionIndex->mine->crystal->get() + $productionIndex->planet_slot->crystal->get())
                    * 0.02
                )
            );
        }

        if ($productionIndex->mine->deuterium->get() > 0) {
            $productionIndex->commanding_staff->deuterium->set(
                floor(
                    ($productionIndex->mine->deuterium->get() + $productionIndex->planet_slot->deuterium->get())
                    * 0.02
                )
            );
        }

        if ($productionIndex->mine->energy->get() > 0) {
            $productionIndex->commanding_staff->energy->set(
                floor($productionIndex->mine->energy->get() * 0.02)
            );
        }
    }

    private function calculateTotal(ProductionIndex $productionIndex): void
    {
        $productionIndex->total->add($productionIndex->basic);
        $productionIndex->total->add($productionIndex->mine);
        $productionIndex->total->add($productionIndex->plasma_technology);
        $productionIndex->total->add($productionIndex->planet_slot);
        $productionIndex->total->add($productionIndex->engineer);
        $productionIndex->total->add($productionIndex->geologist);
        $productionIndex->total->add($productionIndex->commanding_staff);
    }
}

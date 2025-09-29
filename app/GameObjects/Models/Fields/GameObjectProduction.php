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
    /**
     * Metal production formula in \Closure format.
     * The function is given two arguments and expects a float return value,
     *      which represents the Metal production amount.
     * Arguments:
     *   1. GameObjectProduction $gameObjectProduction
     *   2. int $level
     *
     * @var ?\Closure
     */
    public ?\Closure $metal_formula = null;

    /**
     * Crystal production formula in \Closure format.
     * The function is given two arguments and expects a float return value,
     *      which represents the Crystal production amount.
     * Arguments:
     *   1. GameObjectProduction $gameObjectProduction
     *   2. int $level
     *
     * @var ?\Closure
     */
    public ?\Closure $crystal_formula = null;

    /**
     * Deuterium production formula in \Closure format.
     * The function is given two arguments and expects a float return value,
     *      which represents the Deuterium production amount.
     * Arguments:
     *   1. GameObjectProduction $gameObjectProduction
     *   2. int $level
     *
     * @var ?\Closure
     */
    public ?\Closure $deuterium_formula = null;

    /**
     * Energy production formula in \Closure format.
     * The function is given two arguments and expects a float return value,
     *      which represents the Energy production amount.
     * Arguments:
     *   1. GameObjectProduction $gameObjectProduction
     *   2. int $level
     *
     * @var ?\Closure
     */
    public ?\Closure $energy_formula = null;

    /**
     * The planet the object production calculation applies to
     *
     * @var PlanetService
     */
    public PlanetService $planetService;

    /**
     * The player the $planetService belongs to
     *
     * @var PlayerService
     */
    public PlayerService $playerService;

    /**
     * The Universe speed, set by a server admin.
     *
     * @var int
     */
    public int $universe_speed = 1;

    /**
     * The player's plasma technology level.
     *
     * @var int
     */
    private int $plasma_technology_level = 0;

    /**
     * Calculates the production index, a listing of production values from each source
     *
     * @param int $level
     *  The building level
     *
     * @param float $building_percentage
     *  The production percentage of the building set by the player
     *
     * @return ProductionIndex
     */
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
        $this->calculatePlasmaTech($productionIndex);
        $this->calculateEngineer($productionIndex);
        $this->calculateGeologist($productionIndex);
        $this->calculateCommandingStaff($productionIndex);
        $this->calculateItems($productionIndex);
        $this->calculateTotal($productionIndex);

        return $productionIndex;
    }

    /**
     * Calculates Mine production only, without any bonuses.
     *
     * @param ProductionIndex $productionIndex
     * @param int $level
     * @param float $building_percentage
     * @return void
     */
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

    /**
     * Calculates Plasma Technology's production bonus amount
     * Plasma Technology affects mine and planet slot bonus.
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
    private function calculatePlasmaTech(ProductionIndex $productionIndex): void
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

    /**
     * Calculates Planet slot bonus only
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
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

    /**
     * Calculates Engineer bonus
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
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

    /**
     * Calculates Geologist bonus
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
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

    /**
     * Calculates Commanding Staff bonus
     * Commanding Staff is activated when a player has all officers activated
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
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

    /**
     * Calculates active item bonuses
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
    private function calculateItems(ProductionIndex $productionIndex): void
    {
        // TODO: when items are added into the game
        //   - do items affect officer bonuses? or any other bonuses?
    }

    /**
     * Calculates total production, sum of all production + bonuses.
     *
     * @param ProductionIndex $productionIndex
     * @return void
     */
    private function calculateTotal(ProductionIndex $productionIndex): void
    {
        $productionIndex->total->add($productionIndex->basic);
        $productionIndex->total->add($productionIndex->mine);
        $productionIndex->total->add($productionIndex->plasma_technology);
        $productionIndex->total->add($productionIndex->planet_slot);
        $productionIndex->total->add($productionIndex->engineer);
        $productionIndex->total->add($productionIndex->geologist);
        $productionIndex->total->add($productionIndex->commanding_staff);
        $productionIndex->total->add($productionIndex->items);
    }
}

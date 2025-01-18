<?php

namespace OGame\Services;

use Exception;
use OGame\Models\DebrisField;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

/**
 * Class DebrisFieldService.
 *
 * Debris field object.
 *
 * @package OGame\Services
 */
class DebrisFieldService
{
    /**
     * The debris field object model.
     *
     * @var DebrisField
     */
    private DebrisField $debrisField;

    /**
     * @var PlayerService
     */
    private PlayerService $playerService;

    /**
     * DebrisFieldService constructor.
     *
     * @param PlayerService $playerService
     */
    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Load an existing debris field or create a new empty one in memory for the given coordinates.
     *
     * @param Coordinate $coordinates
     */
    public function loadOrCreateForCoordinates(Coordinate $coordinates): void
    {
        $debrisField = DebrisField::where('galaxy', $coordinates->galaxy)
            ->where('system', $coordinates->system)
            ->where('planet', $coordinates->position)
            ->first();

        if (!$debrisField) {
            $debrisField = new DebrisField();
            $debrisField->galaxy = $coordinates->galaxy;
            $debrisField->system = $coordinates->system;
            $debrisField->planet = $coordinates->position;
            $debrisField->metal = 0;
            $debrisField->crystal = 0;
            $debrisField->deuterium = 0;
        }

        $this->debrisField = $debrisField;
    }

    /**
     * Load debris field by coordinate only if it exists.
     *
     * @param Coordinate $coordinate
     * The coordinate of the debris field.
     *
     * @return bool True if the debris field exists and was loaded successfully, false otherwise.
     */
    public function loadForCoordinates(Coordinate $coordinate): bool
    {
        // Fetch planet model
        $debrisField = DebrisField::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->first();

        if ($debrisField !== null) {
            $this->debrisField = $debrisField;
            return true;
        }

        return false;
    }

    /**
     * Get the coordinates of the debris field.
     */
    public function getCoordinates(): Coordinate
    {
        return new Coordinate($this->debrisField->galaxy, $this->debrisField->system, $this->debrisField->planet);
    }

    /**
     * Reloads the debris field object from the database.
     *
     * @return void
     */
    public function reload(): void
    {
        $this->loadForCoordinates($this->getCoordinates());
    }

    /**
     * Returns the resources of the debris field.
     *
     * @return Resources
     */
    public function getResources(): Resources
    {
        if ($this->debrisField->metal === null) {
            return new Resources(0, 0, 0, 0);
        }

        return new Resources($this->debrisField->metal, $this->debrisField->crystal, $this->debrisField->deuterium, 0);
    }

    /**
     * Append resources to an existing debris field.
     *
     * @param Resources $resources
     * @return void
     */
    public function appendResources(Resources $resources): void
    {
        if (!isset($this->debrisField)) {
            $this->debrisField = new DebrisField();
        }

        $this->debrisField->metal += $resources->metal->get();
        $this->debrisField->crystal += $resources->crystal->get();
        $this->debrisField->deuterium += $resources->deuterium->get();
    }

    /**
     * Deduct resources from an existing debris field.
     *
     * @param Resources $resources
     * @return void
     * @throws Exception
     */
    public function deductResources(Resources $resources): void
    {
        if (!isset($this->debrisField)) {
            $this->debrisField = new DebrisField();
        }

        $metalToDeduct = $resources->metal->get();
        $crystalToDeduct = $resources->crystal->get();
        $deuteriumToDeduct = $resources->deuterium->get();

        if ($this->debrisField->metal < $metalToDeduct ||
            $this->debrisField->crystal < $crystalToDeduct ||
            $this->debrisField->deuterium < $deuteriumToDeduct) {
            throw new Exception('Not enough resources in the debris field');
        }

        $this->debrisField->metal -= $metalToDeduct;
        $this->debrisField->crystal -= $crystalToDeduct;
        $this->debrisField->deuterium -= $deuteriumToDeduct;
    }

    /**
     * Save the debris field to the database.
     *
     * @return void
     */
    public function save(): void
    {
        $this->debrisField->save();
    }

    /**
     * Delete the debris field from the database.
     *
     * @return void
     */
    public function delete(): void
    {
        if (isset($this->debrisField) && $this->debrisField->exists) {
            $this->debrisField->delete();
            $this->debrisField = new DebrisField();
        }
    }

    /**
     * Calculate the number of recyclers needed to recycle the entire debris field.
     *
     * @return int The number of recyclers needed.
     */
    public function calculateRequiredRecyclers(): int
    {
        $recycler = ObjectService::getUnitObjectByMachineName('recycler');
        $recyclerCapacity = $recycler->properties->capacity->calculate($this->playerService)->totalValue;

        $totalDebris = $this->debrisField->metal + $this->debrisField->crystal + $this->debrisField->deuterium;
        return (int) ceil($totalDebris / $recyclerCapacity);
    }
}

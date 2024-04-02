<?php

namespace OGame\Services;

use Exception;
use OGame\Factories\PlanetServiceFactory;
use OGame\Planet as Planet;

/**
 * Class PlanetList.
 *
 * Wrapper object which can contain one or more Planet objects.
 *
 * @package OGame\Services
 */
class PlanetListService
{

    /**
     * The planet object from the model.
     *
     * @var array
     */
    protected array $planets = [];

    /**
     * PlayerService
     *
     * @var PlayerService
     */
    protected PlayerService $player;

    /**
     * Planets constructor.
     */
    public function __construct(PlayerService $player)
    {
        $this->player = $player;
        $this->load($player->getId());
    }

    /**
     * Load all planets of specific user.
     */
    public function load($id)
    {
        // Get all planets of user
        $planets = Planet::where('user_id', $id)->get();
        foreach ($planets as $record) {
            $planetServiceFactory = app()->make(PlanetServiceFactory::class);
            $planetService = $planetServiceFactory->makeForPlayer($this->player, $record->id);

            $this->planets[] = $planetService;
        }

        // If no planets, create at least one.
        if (count($this->planets) < 2) {
            // TODO: move this logic to the user creation logic as well as the tech records.
            // As a test: give all players two random planets. (this should be just one, uncomment the below after dev)

            $planetServiceFactory = app()->make(PlanetServiceFactory::class);
            $planetService = $planetServiceFactory->createForPlayer($this->player);

            $this->planets[] = $planetService;
        }
        /*if (empty($this->planets)) {
            $planet = resolve('OGame\Services\PlanetService');
            $planet->create($id);

            $this->planets[] = $planet;
        }*/
    }

    /**
     * Updates all planets in this planet list.
     */
    public function update()
    {
        foreach ($this->planets as $planet) {
            $planet->update();
        }
    }

    /**
     * Get already loaded child planet by ID. Invokes an exception if the
     * planet is not found.
     */
    public function childPlanetById($id)
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() == $id) {
                return $planet;
            }
        }

        throw new Exception('Requested planet is not owned by this player.');
    }

    /**
     * Checks whether planet with given ID exists and is owned by the current user.
     *
     * @param $id
     * @return bool
     */
    public function planetExistsAndOwnedByPlayer($id)
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns current planet of player.
     */
    public function current()
    {
        // Get current planet from PlayerService object.
        $currentPlanetId = $this->player->getCurrentPlanetId();

        // Check if this planet actually exists before returning it.
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() == $currentPlanetId) {
                return $planet;
            }
        }

        // No valid current planet set, return first planet instead.
        return $this->first();
    }

    /**
     * Get first planet of player.
     */
    public function first()
    {
        return $this->planets[0];
    }

    /**
     * Return array of planet objects.
     */
    public function all()
    {
        return $this->planets;
    }

    /**
     * Get amount of planets.
     */
    public function count()
    {
        return count($this->planets);
    }
}

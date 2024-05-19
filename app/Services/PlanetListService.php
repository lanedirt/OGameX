<?php

namespace OGame\Services;

use Exception;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet as Planet;

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
     * @var array<PlanetService>
     */
    private array $planets = [];

    /**
     * PlayerService
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * @var PlanetServiceFactory $planetServiceFactory
     */
    private PlanetServiceFactory $planetServiceFactory;

    /**
     * Planets constructor.
     */
    public function __construct(PlayerService $player, PlanetServiceFactory $planetServiceFactory)
    {
        $this->planetServiceFactory = $planetServiceFactory;
        $this->player = $player;
        $this->load($player->getId());
    }

    /**
     * Load all planets of specific user.
     *
     * @param int $id
     * @return void
     */
    public function load(int $id): void
    {
        // Get all planets of user
        $planets = Planet::where('user_id', $id)->get();
        foreach ($planets as $record) {
            $planetService = $this->planetServiceFactory->makeForPlayer($this->player, $record->id);
            $this->planets[] = $planetService;
        }

        // If no planets, create at least one.
        if (count($this->planets) < 1) {
            // TODO: move this logic to the user creation logic as well as the tech records.
            // For testing purposes: give all players two random planets at registration.
            // Normally it should be just the Homeworld.
            $planetNames = ['Homeworld', 'Colony'];
            for ($i = 0; $i <= (2 - count($this->planets)); $i++) {
                $planetService = $this->planetServiceFactory->createInitialForPlayer($this->player, $planetNames[$i]);
                $this->planets[] = $planetService;
            }

            // Send welcome message to player
            // TODO: move this to the user creation logic.
            $message = new MessageService($this->player);
            $message->sendWelcomeMessage();
        }
    }

    /**
     * Updates all planets in this planet list.
     *
     * @return void
     * @throws Exception
     */
    public function update(): void
    {
        foreach ($this->planets as $planet) {
            $planet->update();
        }
    }

    /**
     * Get already loaded child planet by ID. Invokes an exception if the
     * planet is not found.
     * @throws Exception
     */
    public function childPlanetById(int $id): PlanetService
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $id) {
                return $planet;
            }
        }

        throw new Exception('Requested planet is not owned by this player.');
    }

    /**
     * Checks whether planet with given ID exists and is owned by the current user.
     *
     * @param int $id
     * @return bool
     */
    public function planetExistsAndOwnedByPlayer(int $id): bool
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns current planet of player.
     */
    public function current(): PlanetService
    {
        // Get current planet from PlayerService object.
        $currentPlanetId = $this->player->getCurrentPlanetId();

        // Check if this planet actually exists before returning it.
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $currentPlanetId) {
                return $planet;
            }
        }

        // No valid current planet set, return first planet instead.
        return $this->first();
    }

    /**
     * Get first planet of player.
     *
     * @return PlanetService
     */
    public function first(): PlanetService
    {
        return $this->planets[0];
    }

    /**
     * Return array of planet objects.
     *
     * @return array<PlanetService>
     */
    public function all(): array
    {
        return $this->planets;
    }

    /**
     * Get amount of planets.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->planets);
    }
}

<?php

namespace OGame\Factories;

use http\Exception\RuntimeException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Planet;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class PlanetServiceFactory
{
    /**
     * Cached instances of planetService.
     *
     * @var array<PlanetService>
     */
    protected array $instances = [];

    /**
     * SettingsService.
     *
     * @var SettingsService
     */
    protected SettingsService $settings;

    /**
     * PlanetServiceFactory constructor.
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settings = $settingsService;
    }

    /**
     * Returns a planetService either from local instances cache or creates a new one. Note:
     * it is advised to use makeForPlayer() method if playerService is already available.
     *
     * @param int $planetId
     * @return PlanetService
     * @throws BindingResolutionException
     */
    public function make(int $planetId): PlanetService
    {
        if (!isset($this->instances[$planetId])) {
            $planetService = app()->make(PlanetService::class, ['player' => null, 'planet_id' => $planetId]);
            $this->instances[$planetId] = $planetService;
        }

        return $this->instances[$planetId];
    }

    /**
     * Returns a planetService either from local instances cache or creates a new one.
     *
     * @param PlayerService $player
     * @param int $planetId
     * @return PlanetService
     * @throws BindingResolutionException
     */
    public function makeForPlayer(PlayerService $player, int $planetId): PlanetService
    {
        if (!isset($this->instances[$planetId])) {
            $planetService = app()->make(PlanetService::class, ['player' => $player, 'planet_id' => $planetId]);
            $this->instances[$planetId] = $planetService;
        }

        return $this->instances[$planetId];
    }

    /**
     * Determine next available new planet position.
     *
     * @return array<string, int>
     */
    public function determineNewPlanetPosition() : array {
        $lastAssignedGalaxy = $this->settings->get('last_assigned_galaxy', 1);
        $lastAssignedSystem = $this->settings->get('last_assigned_system', 1);

        $galaxy = $lastAssignedGalaxy;
        $system = $lastAssignedSystem;

        $tryCount = 0;
        while ($tryCount < 100) {
            $tryCount++;
            $planetCount = Planet::where('galaxy', $galaxy)->where('system', $system)->count();

            // 70% of the time max 2 planets per system, 30% of the time max 3.
            if ($planetCount < ((rand(1, 10) < 7) ? 2 : 3)) {
                // Find a random position between 4 and 12 that's not already taken
                $positions = range(4, 12);
                shuffle($positions); // Randomize the positions array

                foreach ($positions as $position) {
                    $existingPlanet = Planet::where('galaxy', $galaxy)->where('system', $system)->where('planet', $position)->first();
                    if (!$existingPlanet) {
                        return ['galaxy' => $galaxy, 'system' => $system, 'position' => $position];
                    }
                }

                // Increment system and galaxy accordingly if no position is found
                $system++;
                if ($system > 499) {
                    $system = 1;
                    $galaxy++;
                }
            } else {
                // Increment system and galaxy if the current one is full
                $system++;
                if ($system > 499) {
                    $system = 1;
                    $galaxy++;
                }
            }
        }

        // If more than 100 tries have been done with no success, give up.
        return [];
    }

    /**
     * Creates a new random planet and then return the planetService instance for it.
     *
     * @param PlayerService $player
     * @param string $planetName
     * @return PlanetService
     * @throws BindingResolutionException
     */
    public function createForPlayer(PlayerService $player, string $planetName = 'Colony'): PlanetService
    {
        $new_position = $this->determineNewPlanetPosition();
        if (empty($new_position['galaxy']) || empty($new_position['system']) || empty($new_position['position'])) {
            // Failed to get a new position for the to be created planet. Throw exception.
            throw new RuntimeException('Unable to determine new planet position.');
        }

        // Position is available
        $planet = new Planet;
        $planet->user_id = $player->getId();
        $planet->name = $planetName;
        $planet->galaxy = $new_position['galaxy'];
        $planet->system = $new_position['system'];
        $planet->planet = $new_position['position'];
        $planet->destroyed = 0;
        $planet->field_current = 0;
        // TODO: figure out how to determine the properties below so it matches the game logic.
        $planet->planet_type = 1; //?
        $planet->diameter = 300;
        $planet->field_max = rand(140, 250);
        $planet->temp_min = rand(0, 100);
        $planet->temp_max = $planet->temp_min + 40;

        $planet->metal = 500;
        $planet->crystal = 500;
        $planet->deuterium = 0;

        // Set default mine production percentages to 10 (100%).
        $planet->metal_mine_percent = 10;
        $planet->crystal_mine_percent = 10;
        $planet->deuterium_synthesizer_percent = 10;
        $planet->solar_plant_percent = 10;
        $planet->fusion_plant_percent = 10;

        $planet->time_last_update = (int)Carbon::now()->timestamp;
        $planet->save();

        // Update settings with the last assigned galaxy and system if they changed.
        $this->settings->set('last_assigned_galaxy', $new_position['galaxy']);
        $this->settings->set('last_assigned_system', $new_position['system']);

        // Now make and return the planetService.
        return $this->makeForPlayer($player, $planet->id);
    }
}
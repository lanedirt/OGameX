<?php

namespace Tests\Feature\NpcEmpire;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\FleetMission;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use Tests\TestCase;
use Throwable;

/**
 * Verify the autonomous expansion of NPC empires: colony ship
 * production and dispatching of colonization missions to nearby
 * free coordinates.
 */
class NpcEmpireExpansionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that an expansion-capable empire with a ready colony ship
     * dispatches a colonization mission (type 7) on AI decision.
     */
    public function testExpansionDispatchesColonizationMission(): void
    {
        $empire = $this->prepareExpansionEmpire(withColonyShip: true);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertSame(
                1,
                FleetMission::where('user_id', $empire->user_id)->where('mission_type', 7)->count(),
                'The expansion AI must dispatch a colonization mission when a colony ship is ready.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that when no colony ship is available the AI queues its
     * production instead of dispatching.
     */
    public function testExpansionQueuesColonyShipWhenMissing(): void
    {
        $empire = $this->prepareExpansionEmpire(withColonyShip: false, shipyardLevel: 4);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertSame(
                0,
                FleetMission::where('user_id', $empire->user_id)->count(),
                'Without a colony ship no mission may be dispatched.'
            );

            $colonyShipId = ObjectService::getObjectByMachineName('colony_ship')->id;
            $this->assertSame(
                1,
                \OGame\Models\UnitQueue::where('planet_id', $this->firstPlanetId($empire))
                    ->where('object_id', $colonyShipId)
                    ->count(),
                'The expansion AI must queue colony ship production when missing.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that an empire at its astrophysics research expansion limit
     * dispatches nothing until research allows more planets.
     */
    public function testExpansionBlockedAtMaxPlanetLimit(): void
    {
        $empire = $this->prepareExpansionEmpire(withColonyShip: true, astrophysicsLevel: 0);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertSame(
                0,
                FleetMission::where('user_id', $empire->user_id)->count(),
                'With astrophysics 0 (max 1 planet) no colonization may be dispatched.'
            );

            $state = $empire->refresh()->ai_state;
            $this->assertGreaterThan(
                (int)Carbon::now()->timestamp + 3000,
                (int)($state['next_expansion_at'] ?? 0),
                'Blocked expansion must schedule a delayed recheck (~1 hour).'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify the dispatched colonization mission settles a new planet at
     * arrival, driven by the regular mission processing.
     */
    public function testColonizationMissionSettlesNewPlanet(): void
    {
        $empire = $this->prepareExpansionEmpire(withColonyShip: true);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        $manager->processAll();

        $this->assertSame(
            1,
            FleetMission::where('user_id', $empire->user_id)->where('mission_type', 7)->count(),
            'A colonization mission must be dispatched.'
        );

        // Travel past fleet duration (colony ships are slow: flight takes
        // several hours in-system) and tick.
        Carbon::setTestNow(Carbon::now()->addHours(8));
        try {
            $manager->processAll();

            $this->assertGreaterThanOrEqual(
                2,
                Planet::where('user_id', $empire->user_id)->count(),
                'The colonization mission must settle a second planet at arrival.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Prepare an NPC empire primed for expansion decisions.
     *
     * @param bool $withColonyShip
     * @param int $astrophysicsLevel
     * @param int $shipyardLevel
     * @return NpcEmpire
     * @throws Throwable
     */
    private function prepareExpansionEmpire(
        bool $withColonyShip,
        int $astrophysicsLevel = 2,
        int $shipyardLevel = 0
    ): NpcEmpire {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        // Force the expansion AI to act on the very next decision.
        $empire->aggression = 0;
        $empire->ai_state = ['next_decision_at' => 0, 'next_expansion_at' => 0];
        $empire->save();

        $player = resolve(\OGame\Factories\PlayerServiceFactory::class)->make($empire->user_id);
        if ($astrophysicsLevel > 0) {
            $player->setResearchLevel('astrophysics', $astrophysicsLevel);
        }
        // Colony ships require impulse_drive >= 3 on top of the shipyard.
        $player->setResearchLevel('impulse_drive', 3);

        $planet = $this->planetService($empire);
        if ($shipyardLevel > 0) {
            $planet->setObjectLevel(ObjectService::getObjectByMachineName('shipyard')->id, $shipyardLevel);
        }

        if ($withColonyShip) {
            $planet->addUnit('colony_ship', 1);
        }

        // Generous fuel and resources for dispatch and production.
        $planet->addResources(new Resources(50000, 50000, 50000));

        return $empire;
    }

    /**
     * Get the planet id of the first (initial) planet of the empire.
     *
     * @param NpcEmpire $empire
     * @return int
     */
    private function firstPlanetId(NpcEmpire $empire): int
    {
        $planet = Planet::where('user_id', $empire->user_id)
            ->where('planet_type', 1)
            ->firstOrFail();

        return (int)$planet->id;
    }

    /**
     * Resolve the PlanetService for the NPC's first planet.
     *
     * @param NpcEmpire $empire
     * @return PlanetService
     * @throws Throwable
     */
    private function planetService(NpcEmpire $empire): PlanetService
    {
        $factory = resolve(\OGame\Factories\PlanetServiceFactory::class);
        $planetService = $factory->make($this->firstPlanetId($empire));
        $this->assertNotNull($planetService, 'Planet service must be resolvable.');

        return $planetService;
    }
}

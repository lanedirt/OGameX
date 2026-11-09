<?php

namespace Tests\Feature\NpcEmpire;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\NpcEmpire;
use OGame\Models\UnitQueue;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use Tests\TestCase;
use Throwable;

/**
 * Verify the autonomous fleet production of NPC empires: units get queued
 * through the real unit queue, scaled by economy and aggression.
 */
class NpcEmpireFleetTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that a shipyard-equipped NPC planet queues units when the
     * AI decides (probes/cargo/fighters/defense candidates).
     */
    public function testFleetProductionQueuesUnitsWithShipyard(): void
    {
        $empire = $this->prepareFleetEmpire(aggression: 100, shipyardLevel: 1);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertGreaterThan(
                0,
                UnitQueue::where('planet_id', $this->firstPlanetId($empire))->count(),
                'The fleet AI must queue unit production on planets with a shipyard.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that without a shipyard no units are queued.
     */
    public function testNoFleetProductionWithoutShipyard(): void
    {
        $empire = $this->prepareFleetEmpire(aggression: 100, shipyardLevel: 0);
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertSame(
                0,
                UnitQueue::where('planet_id', $this->firstPlanetId($empire))->count(),
                'Without a shipyard the fleet AI must not queue anything.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that a peaceful empire (aggression 0) never queues defense
     * units and an aggressive one does once its shipyard supports it.
     */
    public function testAggressionScalesDefenseProduction(): void
    {
        // Peaceful empire: rocket launchers must never be queued.
        $peaceful = $this->prepareFleetEmpire(aggression: 0, shipyardLevel: 2);
        resolve(NpcEmpireManager::class)->processAll();

        $rocketLauncherId = ObjectService::getObjectByMachineName('rocket_launcher')->id;
        $this->assertSame(
            0,
            UnitQueue::where('planet_id', $this->firstPlanetId($peaceful))
                ->where('object_id', $rocketLauncherId)
                ->count(),
            'Aggression 0 empires must not build defense units.'
        );

        // Aggressive empire with an eligible shipyard: defense gets queued.
        $aggressive = $this->prepareFleetEmpire(aggression: 100, shipyardLevel: 2);
        resolve(NpcEmpireManager::class)->processAll();

        $this->assertGreaterThan(
            0,
            UnitQueue::where('planet_id', $this->firstPlanetId($aggressive))
                ->where('object_id', $rocketLauncherId)
                ->count(),
            'Max aggression empires must build defense units.'
        );
    }

    /**
     * Verify that produced units actually land on the planet over time.
     */
    public function testQueuedUnitsAreDeliveredOverTime(): void
    {
        $empire = $this->prepareFleetEmpire(aggression: 100, shipyardLevel: 2);
        $manager = resolve(NpcEmpireManager::class);

        $manager->processAll();

        $planet = $this->planetService($empire);

        // Travel long enough for the queued units to be produced.
        Carbon::setTestNow(Carbon::now()->addHours(6));
        try {
            $manager->processAll();

            $totalUnits = 0;
            foreach (['small_cargo', 'light_fighter', 'rocket_launcher', 'espionage_probe'] as $machine) {
                $totalUnits += $planet->getObjectAmount($machine);
            }

            $this->assertGreaterThan(
                0,
                $totalUnits,
                'Queued units must be delivered onto the planet by the unit queue over time.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Prepare an NPC empire primed for fleet production decisions.
     *
     * @param int $aggression
     * @param int $shipyardLevel
     * @return NpcEmpire
     * @throws Throwable
     */
    private function prepareFleetEmpire(int $aggression, int $shipyardLevel): NpcEmpire
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $empire->aggression = $aggression;
        $empire->ai_state = ['next_decision_at' => 0];
        $empire->save();

        $planet = $this->planetService($empire);

        // Deep mines supply the economy so unit targets exceed zero.
        $planet->setObjectLevel(ObjectService::getObjectByMachineName('metal_mine')->id, 10);
        $planet->setObjectLevel(ObjectService::getObjectByMachineName('crystal_mine')->id, 10);
        if ($shipyardLevel > 0) {
            $planet->setObjectLevel(ObjectService::getObjectByMachineName('shipyard')->id, $shipyardLevel);
        }

        // Generous resources so all affordability checks pass.
        $planet->addResources(new \OGame\Models\Resources(200000, 200000, 20000));

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
        $planet = \OGame\Models\Planet::where('user_id', $empire->user_id)
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

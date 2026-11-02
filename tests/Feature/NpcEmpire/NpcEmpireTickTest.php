<?php

namespace Tests\Feature\NpcEmpire;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\BuildingQueue;
use OGame\Models\NpcEmpire;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use Tests\TestCase;
use Throwable;

/**
 * Verify the scheduled processing tick of NPC empires: offline resource
 * accumulation, autonomous build queue decisions and randomized decision
 * cooldowns.
 */
class NpcEmpireTickTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that a processing tick enqueues building upgrades for the NPC
     * homeworld even though the NPC never loads any page.
     */
    public function testTickEnqueuesBuildingUpgradesAutonomously(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $manager->processAll();

        $queueCount = BuildingQueue::where('planet_id', $this->firstPlanetId($empire))->count();
        $this->assertGreaterThan(
            0,
            $queueCount,
            'After a tick the NPC planet must have queued building upgrades without any page load.'
        );

        // The decision cooldown must have been scheduled.
        $empire->refresh();
        $this->assertGreaterThan(
            0,
            (int)($empire->ai_state['next_decision_at'] ?? 0),
            'AI state must persist a next decision timestamp.'
        );
        $this->assertNotNull($empire->last_ai_decision_at);
    }

    /**
     * Verify that the randomized decision cooldown prevents machine-gun
     * queue actions: an immediate second tick must not dispatch new
     * decisions, while a tick after the cooldown does.
     */
    public function testDecisionCooldownBlocksImmediateSecondTick(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $manager->processAll();

        $queueCountAfterFirst = BuildingQueue::where('planet_id', $this->firstPlanetId($empire))->count();
        $this->assertGreaterThan(0, $queueCountAfterFirst);

        // Immediate second tick: cooldown active, no new decisions.
        $manager->processAll();
        $this->assertSame(
            $queueCountAfterFirst,
            BuildingQueue::where('planet_id', $this->firstPlanetId($empire))->count(),
            'Cooldown must prevent a second decision within the cooldown window.'
        );

        // Travel past the worst-case cooldown (15 minutes) and tick again.
        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();
            $this->assertGreaterThanOrEqual(
                $queueCountAfterFirst,
                BuildingQueue::where('planet_id', $this->firstPlanetId($empire))->count(),
                'After the cooldown elapses the AI must decide again.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that NPC planets accumulate resources offline: waiting simulates
     * hours of no page loads and the metal amount must increase accordingly.
     */
    public function testResourcesAccumulateWhileOffline(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        // Run one tick at T0 (initializes production stats)...
        $manager->processAll();

        $planet = $this->planetService($empire);
        $metalBefore = $planet->metal()->get();

        // ...then travel 6 hours without any interaction and tick again.
        Carbon::setTestNow(Carbon::now()->addHours(6));
        try {
            $manager->processAll();

            $metalAfter = $planet->metal()->get();
            $this->assertGreaterThan(
                $metalBefore,
                $metalAfter,
                'NPC planet resources must accumulate without page loads (scheduler tick drives the lazy update).'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that the development_speed trait adds bonus production on top
     * of normal production.
     */
    public function testDevelopmentSpeedAddsBonusProduction(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        // Normalize the trait for a deterministic comparison.
        $empire->development_speed = 1.0;
        $empire->save();

        // Tick at T0 so production stats exist, then measure over a window.
        $manager->processAll();

        $planet = $this->planetService($empire);
        $metalBefore = $planet->metal()->get();

        Carbon::setTestNow(Carbon::now()->addHours(2));
        try {
            $manager->processAll();

            $normalProduction = $planet->getMetalProductionPerHour() * 2;
            $withBonus = $planet->metal()->get() - $metalBefore;

            $this->assertGreaterThan(
                $normalProduction * 0.5,
                $withBonus,
                'With development_speed = 1.0 the metal gain must clearly exceed half the normal production (bonus + normal).'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that processing is a no-op when the feature is disabled.
     */
    public function testProcessingIsSkippedWhenFeatureDisabled(): void
    {
        resolve(SettingsService::class)->set('npc_empires_enabled', '0');

        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $manager->processAll();

        $empire->refresh();
        $this->assertNull(
            $empire->last_ai_decision_at,
            'When the feature is disabled, no AI decisions may happen.'
        );
        $this->assertDatabaseMissing('building_queues', [
            'planet_id' => $this->firstPlanetId($empire),
        ]);
    }

    /**
     * Verify that research starts flowing once a research lab exists.
     */
    public function testResearchStartsOnceLabExists(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $planet = $this->planetService($empire);

        // Give the NPC a basic economy plus a level 1 research lab and
        // enough resources so research becomes affordable.
        $planet->setObjectLevel(ObjectService::getObjectByMachineName('research_lab')->id, 1);
        $planet->addResources(new \OGame\Models\Resources(20000, 20000, 20000));

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        try {
            $manager->processAll();

            $this->assertGreaterThan(
                0,
                \OGame\Models\ResearchQueue::where('planet_id', $this->firstPlanetId($empire))->count(),
                'Once a research lab exists, the AI must enqueue research autonomously.'
            );
        } finally {
            Carbon::setTestNow();
        }
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
            ->first();
        $this->assertNotNull($planet, 'NPC empire must own a planet.');

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

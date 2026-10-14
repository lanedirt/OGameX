<?php

namespace Tests\Feature\NpcEmpire;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\EspionageReport;
use OGame\Models\FleetMission;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Tests\TestCase;

/**
 * Verify the active competition AI of NPC empires: espionage of nearby
 * players, target evaluation and raids, gated by the server-level attack
 * toggle.
 */
class NpcEmpireWarTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that with the global attack toggle disabled the war AI does
     * nothing, even when aggressive and equipped.
     */
    public function testWarAiDisabledDoesNothing(): void
    {
        resolve(SettingsService::class)->set('npc_attack_players_enabled', '0');

        $fixture = $this->prepareWarFixture();
        resolve(NpcEmpireManager::class)->processAll();

        $this->assertSame(
            0,
            $this->outboundMissionCount($fixture['empire'], 6) + $this->outboundMissionCount($fixture['empire'], 1),
            'With npc_attack_players_enabled=0 the war AI must not dispatch anything.'
        );
    }

    /**
     * Verify that an equipped aggressive NPC probes a nearby player
     * planet within the same galaxy.
     */
    public function testWarAiProbesNearbyPlayer(): void
    {
        resolve(SettingsService::class)->set('npc_attack_players_enabled', '1');

        $fixture = $this->prepareWarFixture();
        resolve(NpcEmpireManager::class)->processAll();

        $this->assertSame(
            1,
            $this->outboundMissionCount($fixture['empire'], 6),
            'The war AI must dispatch an espionage mission to the nearby player.'
        );
    }

    /**
     * Verify that the probe in-flight window prevents duplicate espionage
     * missions within a short period.
     */
    public function testProbeInFlightWindowPreventsDoubleProbe(): void
    {
        resolve(SettingsService::class)->set('npc_attack_players_enabled', '1');

        $fixture = $this->prepareWarFixture();
        $manager = resolve(NpcEmpireManager::class);

        Carbon::setTestNow(Carbon::now()->addMinutes(16));
        $manager->processAll();

        Carbon::setTestNow(Carbon::now()->addMinutes(5));
        try {
            $manager->processAll();

            $this->assertSame(
                1,
                $this->outboundMissionCount($fixture['empire'], 6),
                'While a probe flight is in progress no second probe may be dispatched.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    /**
     * Verify that fresh intel of an undefended, wealthy target results in
     * a raid fleet (fighters + cargo for the loot).
     */
    public function testWarAiRaidsWeakWealthyTarget(): void
    {
        resolve(SettingsService::class)->set('npc_attack_players_enabled', '1');

        $fixture = $this->prepareWarFixture(withIntel: true);
        resolve(NpcEmpireManager::class)->processAll();

        $this->assertSame(
            0,
            $this->outboundMissionCount($fixture['empire'], 6),
            'With usable fresh intel the AI must not probe again.'
        );

        $raid = FleetMission::where('user_id', $fixture['empire']->user_id)
            ->where('mission_type', 1)
            ->where(static function ($query): void {
                $query->where('parent_id', 0)->orWhereNull('parent_id');
            })
            ->first();
        $this->assertNotNull($raid, 'The war AI must dispatch an attack on the weak target.');
        $this->assertGreaterThan(0, $raid->light_fighter, 'Raid fleet must contain fighters.');
        $this->assertGreaterThan(0, $raid->small_cargo, 'Raid fleet must contain cargo capacity for loot.');
    }

    /**
     * Verify that a heavily defended target (even when wealthy) is not
     * attacked and is backed off for later.
     */
    public function testWarAiSkipsStronglyDefendedTarget(): void
    {
        resolve(SettingsService::class)->set('npc_attack_players_enabled', '1');

        $fixture = $this->prepareWarFixture(withIntel: true, defense: 100);
        resolve(NpcEmpireManager::class)->processAll();

        $this->assertSame(
            0,
            FleetMission::where('user_id', $fixture['empire']->user_id)->where('mission_type', 1)->count(),
            'A 100 rocket launcher defense must not be attacked by a small raid fleet.'
        );

        $key = $fixture['coordinate']->galaxy . ':' . $fixture['coordinate']->system . ':' . $fixture['coordinate']->position;
        $war = $fixture['empire']->refresh()->ai_state['war'] ?? [];
        $this->assertArrayHasKey($key, $war, 'The target must be memorized in the war state.');
        $this->assertGreaterThan(
            (int)Carbon::now()->timestamp + 3600,
            (int)($war[$key]['recheck_at'] ?? 0),
            'A rejected target must be backed off for hours, not retried next cycle.'
        );
    }

    /**
     * Get the count of outbound war missions (excludes return legs) the
     * NPC empire has dispatched.
     *
     * @param NpcEmpire $empire
     * @param int $missionType
     * @return int
     */
    private function outboundMissionCount(NpcEmpire $empire, int $missionType): int
    {
        return FleetMission::where('user_id', $empire->user_id)
            ->where('mission_type', $missionType)
            ->where(static function ($query): void {
                $query->where('parent_id', 0)->orWhereNull('parent_id');
            })
            ->count();
    }

    /**
     * Prepare an NPC empire plus a neighboring player planet and return
     * all fixture handles.
     *
     * @param bool $withIntel Seed a fresh espionage report about the target.
     * @param int $defense Amount of rocket launchers on the target.
     * @return array{empire: NpcEmpire, coordinate: Coordinate, targetUser: User}
     */
    private function prepareWarFixture(bool $withIntel = false, int $defense = 0): array
    {
        // Move the seeded Legor account far away so its planet cannot be
        // picked as a war candidate near the NPC homeworld.
        \OGame\Models\Planet::query()
            ->where('user_id', User::query()->where('username', 'Legor')->firstOrFail()->id)
            ->update(['galaxy' => 9, 'system' => 450]);

        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        $empire->aggression = 100;
        $empire->ai_state = ['next_decision_at' => 0, 'next_expansion_at' => (int)Carbon::now()->timestamp + 99999];
        $empire->save();

        // Find an empty coordinate next to the NPC homeworld.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $homeworld = $planetServiceFactory->make($this->firstPlanetId($empire));
        $this->assertNotNull($homeworld, 'NPC homeworld must be resolvable.');
        $origin = $homeworld->getPlanetCoordinates();
        $coordinate = new Coordinate($origin->galaxy, $origin->system + 1, 5);
        while ($planetServiceFactory->planetExistsAtCoordinate($coordinate)) {
            $coordinate->position++;
            if ($coordinate->position > 12) {
                $coordinate->position = 4;
                $coordinate->system++;
            }
        }

        // Create the target player planet.
        $targetUser = User::factory()->create();
        $targetPlayer = resolve(PlayerServiceFactory::class)->make($targetUser->id);
        $this->assertNotNull($targetPlayer, 'Target player service must be resolvable.');
        $targetPlanet = $planetServiceFactory->createPlanetAtPosition($targetPlayer, $coordinate, 'RaidTarget');
        if ($defense > 0) {
            $targetPlanet->addUnit('rocket_launcher', $defense);
        }

        // Arm the NPC: raid fleet, probes, plenty of fuel.
        $homeworld->addUnit('light_fighter', 50);
        $homeworld->addUnit('small_cargo', 20);
        $homeworld->addUnit('espionage_probe', 10);
        $homeworld->addResources(new Resources(100000, 100000, 100000));

        if ($withIntel) {
            $report = new EspionageReport();
            $report->planet_galaxy = $coordinate->galaxy;
            $report->planet_system = $coordinate->system;
            $report->planet_position = $coordinate->position;
            $report->planet_type = 1;
            $report->planet_user_id = $targetUser->id;
            $report->player_info = ['player_id' => (string)$targetUser->id, 'player_name' => $targetUser->username];
            $report->resources = ['metal' => 100000, 'crystal' => 80000, 'deuterium' => 20000, 'energy' => 0];
            $report->ships = [];
            $report->defense = $defense > 0 ? ['rocket_launcher' => $defense] : [];
            $report->buildings = [];
            $report->research = [];
            $report->created_at = \Illuminate\Support\Carbon::now()->subMinutes(30);
            $report->save();

            // Intel is trusted when it post-dates the AI's own probe run.
            $key = $coordinate->galaxy . ':' . $coordinate->system . ':' . $coordinate->position;
            $empire->ai_state = [
                'next_decision_at' => 0,
                'next_expansion_at' => (int)Carbon::now()->timestamp + 99999,
                'war' => [$key => ['probed_at' => (int)Carbon::now()->timestamp - 3600]],
            ];
            $empire->save();
        }

        return ['empire' => $empire, 'coordinate' => $coordinate, 'targetUser' => $targetUser];
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
}

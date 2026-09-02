<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\BattleReport;
use OGame\Models\User;
use Tests\TestCase;

/**
 * Tests for the one-time military statistics migration command.
 *
 * Battle reports store the fleet as it stood at the start of the battle, so the migration
 * must derive points from the recorded resource loss instead of from those unit lists.
 */
class MigrateMilitaryStatisticsCommandTest extends TestCase
{
    use DatabaseTransactions;

    private User $attacker;
    private User $defender;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attacker = User::factory()->create();
        $this->defender = User::factory()->create();
    }

    /**
     * A battle where nothing died must not credit anyone, even though both fleets are
     * listed on the report.
     */
    public function testBattleWithoutLossesCreditsNoPoints(): void
    {
        $this->createBattleReport(attackerResourceLoss: 0, defenderResourceLoss: 0);

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:migrate:military-statistics')->assertSuccessful();

        $this->attacker->refresh();
        $this->defender->refresh();

        $this->assertEquals(0, $this->attacker->military_units_lost_points);
        $this->assertEquals(0, $this->attacker->military_units_destroyed_points);
        $this->assertEquals(0, $this->defender->military_units_lost_points);
        $this->assertEquals(0, $this->defender->military_units_destroyed_points);
    }

    /**
     * Each player is credited their own losses as lost points and the opponent's losses
     * as destroyed points.
     */
    public function testResourceLossIsCreditedToBothPlayers(): void
    {
        $this->createBattleReport(attackerResourceLoss: 1_000_000, defenderResourceLoss: 250_000);

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:migrate:military-statistics')->assertSuccessful();

        $this->attacker->refresh();
        $this->defender->refresh();

        // Losses are a fraction of the fleets on the report: reading the unit lists would
        // have credited 4000 / 1000 points instead.
        $this->assertEquals(1000, $this->attacker->military_units_lost_points);
        $this->assertEquals(250, $this->attacker->military_units_destroyed_points);
        $this->assertEquals(250, $this->defender->military_units_lost_points);
        $this->assertEquals(1000, $this->defender->military_units_destroyed_points);
    }

    /**
     * Reports written before resource loss was tracked must be skipped rather than
     * falling back to the starting fleet.
     */
    public function testReportWithoutResourceLossIsSkipped(): void
    {
        $report = $this->createBattleReport(attackerResourceLoss: 0, defenderResourceLoss: 0);

        $attacker = $report->attacker ?? [];
        $defender = $report->defender ?? [];
        unset($attacker['resource_loss'], $defender['resource_loss']);
        $report->attacker = $attacker;
        $report->defender = $defender;
        $report->save();

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:migrate:military-statistics')->assertSuccessful();

        $this->attacker->refresh();
        $this->defender->refresh();

        $this->assertEquals(0, $this->attacker->military_units_lost_points);
        $this->assertEquals(0, $this->defender->military_units_destroyed_points);
    }

    /**
     * Create a battle report between the two test users, with a sizeable fleet on both
     * sides so that reading the unit lists instead of the losses would be obvious.
     */
    private function createBattleReport(int $attackerResourceLoss, int $defenderResourceLoss): BattleReport
    {
        $report = new BattleReport();
        $report->planet_galaxy = 1;
        $report->planet_system = 1;
        $report->planet_position = 1;
        $report->planet_user_id = $this->defender->id;
        $report->general = [];
        $report->attacker = [
            'player_id' => $this->attacker->id,
            'resource_loss' => $attackerResourceLoss,
            // 1000 light fighters = 4,000,000 resources at battle start.
            'units' => ['light_fighter' => 1000],
        ];
        $report->defender = [
            'player_id' => $this->defender->id,
            'resource_loss' => $defenderResourceLoss,
            // 500 rocket launchers = 1,000,000 resources at battle start.
            'units' => ['rocket_launcher' => 500],
        ];
        $report->rounds = [];
        $report->save();

        return $report;
    }
}

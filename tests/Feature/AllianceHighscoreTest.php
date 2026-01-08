<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use OGame\Models\Alliance;
use OGame\Models\AllianceHighscore;
use OGame\Models\Highscore;
use OGame\Models\User;
use OGame\Services\AllianceService;
use Tests\AccountTestCase;

/**
 * Test alliance highscore functionality.
 */
class AllianceHighscoreTest extends AccountTestCase
{
    /**
     * Generate a unique alliance tag for testing.
     */
    private function uniqueTag(string $prefix = 'TST'): string
    {
        return $prefix . substr(md5(uniqid((string) mt_rand(), true)), 0, 5);
    }

    /**
     * Generate a unique alliance name for testing.
     */
    private function uniqueName(string $prefix = 'Test Alliance'): string
    {
        return $prefix . ' ' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8);
    }

    /**
     * Test that alliance highscore is created when alliance is formed.
     */
    public function testAllianceHighscoreRelationship(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Alliance should not have highscore yet (only created by command)
        $this->assertNull($alliance->highscore);
    }

    /**
     * Test generating alliance highscores.
     */
    public function testGenerateAllianceHighscores(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance with founder
        $founder = User::factory()->create();
        $alliance = $allianceService->createAlliance($founder->id, $this->uniqueTag(), $this->uniqueName());

        // Add another member
        $member = User::factory()->create();
        $application = $allianceService->applyToAlliance($member->id, $alliance->id);
        $allianceService->acceptApplication($application->id, $founder->id);

        // Create highscores for both members AFTER all alliance operations are complete
        Highscore::updateOrCreate(['player_id' => $founder->id], ['general' => 1000, 'economy' => 500, 'research' => 300, 'military' => 200]);
        Highscore::updateOrCreate(['player_id' => $member->id], ['general' => 800, 'economy' => 400, 'research' => 250, 'military' => 150]);

        // Generate alliance highscores
        Artisan::call('ogamex:generate-alliance-highscores');

        // Check alliance highscore
        $alliance->refresh();
        /** @var AllianceHighscore $allianceHighscore */
        $allianceHighscore = $alliance->highscore;

        $this->assertNotNull($allianceHighscore);
        $this->assertEquals(1800, $allianceHighscore->general); // 1000 + 800
        $this->assertEquals(900, $allianceHighscore->economy); // 500 + 400
        $this->assertEquals(550, $allianceHighscore->research); // 300 + 250
        $this->assertEquals(350, $allianceHighscore->military); // 200 + 150
    }

    /**
     * Test generating alliance highscore ranks.
     */
    public function testGenerateAllianceHighscoreRanks(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create first alliance
        $founder1 = User::factory()->create();
        $alliance1 = $allianceService->createAlliance($founder1->id, $this->uniqueTag('TOP'), $this->uniqueName('Top Alliance'));

        Highscore::updateOrCreate(['player_id' => $founder1->id], ['general' => 2000, 'economy' => 1000, 'research' => 800, 'military' => 500]);

        // Create second alliance
        $founder2 = User::factory()->create();
        $alliance2 = $allianceService->createAlliance($founder2->id, $this->uniqueTag('MID'), $this->uniqueName('Mid Alliance'));

        Highscore::updateOrCreate(['player_id' => $founder2->id], ['general' => 1000, 'economy' => 500, 'research' => 400, 'military' => 200]);

        // Generate highscores
        Artisan::call('ogamex:generate-alliance-highscores');

        // Generate ranks (includes both player and alliance ranks)
        Artisan::call('ogamex:generate-highscore-ranks');

        // Check ranks
        $alliance1->refresh();
        $alliance2->refresh();

        /** @var AllianceHighscore $highscore1 */
        $highscore1 = $alliance1->highscore;
        /** @var AllianceHighscore $highscore2 */
        $highscore2 = $alliance2->highscore;

        $this->assertNotNull($highscore1);
        $this->assertNotNull($highscore2);

        // Alliance 1 should have higher rank (lower number) than alliance 2
        $this->assertLessThan($highscore2->general_rank, $highscore1->general_rank);
        $this->assertLessThan($highscore2->economy_rank, $highscore1->economy_rank);
        $this->assertLessThan($highscore2->research_rank, $highscore1->research_rank);
        $this->assertLessThan($highscore2->military_rank, $highscore1->military_rank);

        // Both should have valid ranks (not null)
        $this->assertNotNull($highscore1->general_rank);
        $this->assertNotNull($highscore2->general_rank);
    }

    /**
     * Test that alliance highscore updates when member scores change.
     */
    public function testAllianceHighscoreUpdatesWithMemberChanges(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $founder = User::factory()->create();
        $alliance = $allianceService->createAlliance($founder->id, $this->uniqueTag(), $this->uniqueName());

        // Initial scores
        Highscore::updateOrCreate(['player_id' => $founder->id], ['general' => 1000, 'economy' => 500, 'research' => 300, 'military' => 200]);

        // Generate initial alliance highscores
        Artisan::call('ogamex:generate-alliance-highscores');

        $alliance->refresh();
        /** @var AllianceHighscore $allianceHighscore */
        $allianceHighscore = $alliance->highscore;
        $this->assertEquals(1000, $allianceHighscore->general);

        // Update founder's score
        Highscore::updateOrCreate(['player_id' => $founder->id], ['general' => 2000, 'economy' => 500, 'research' => 300, 'military' => 200]);

        // Regenerate alliance highscores
        Artisan::call('ogamex:generate-alliance-highscores');

        $alliance->refresh();
        /** @var AllianceHighscore $allianceHighscoreUpdated */
        $allianceHighscoreUpdated = $alliance->highscore;
        $this->assertEquals(2000, $allianceHighscoreUpdated->general);
    }

    /**
     * Test that alliance highscore is deleted when alliance is disbanded.
     */
    public function testAllianceHighscoreDeletedWhenAllianceDisbanded(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create highscore
        Highscore::updateOrCreate(['player_id' => $this->currentUserId], ['general' => 1000, 'economy' => 500, 'research' => 300, 'military' => 200]);
        Artisan::call('ogamex:generate-alliance-highscores');

        $alliance->refresh();
        /** @var AllianceHighscore $allianceHighscore */
        $allianceHighscore = $alliance->highscore;
        $allianceHighscoreId = $allianceHighscore->id;

        // Disband alliance
        $allianceService->disbandAlliance($alliance->id, $this->currentUserId);

        // Check that highscore is deleted
        $this->assertNull(AllianceHighscore::find($allianceHighscoreId));
    }

    /**
     * Test validRanks scope.
     */
    public function testValidRanksScope(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create highscore without ranks
        AllianceHighscore::create([
            'alliance_id' => $alliance->id,
            'general' => 1000,
            'economy' => 500,
            'research' => 300,
            'military' => 200,
        ]);

        // This specific alliance should not appear in validRanks
        $alliance->refresh();
        /** @var AllianceHighscore $allianceHighscore */
        $allianceHighscore = $alliance->highscore;
        $this->assertNull($allianceHighscore->general_rank);
        $this->assertFalse(AllianceHighscore::validRanks()->where('alliance_id', $alliance->id)->exists());

        // Update with ranks
        $allianceHighscore->update([
            'general_rank' => 1,
            'economy_rank' => 1,
            'research_rank' => 1,
            'military_rank' => 1,
        ]);

        // Should now appear in validRanks
        $this->assertTrue(AllianceHighscore::validRanks()->where('alliance_id', $alliance->id)->exists());
    }
}

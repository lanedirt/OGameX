<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Tests for the scheduled command that permanently deletes inactive players (issue #922).
 */
class DeleteInactivePlayersCommandTest extends AccountTestCase
{
    private const COMMAND = 'ogamex:scheduler:delete-inactive-players';

    /** @var array<int, int> User IDs created during tests, deleted in tearDown. */
    private array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdUserIds)) {
            DB::table('users_tech')->whereIn('user_id', $this->createdUserIds)->delete();
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    /**
     * Set the inactivity deletion threshold (in days).
     */
    private function setDeletionDays(int $days): void
    {
        resolve(SettingsService::class)->set('inactive_player_deletion_days', $days);
    }

    /**
     * Set the last-activity timestamp of a user (stored as a UNIX timestamp string).
     */
    private function setLastActivityDaysAgo(int $userId, int $daysAgo): void
    {
        $user = User::findOrFail($userId);
        $user->time = (string)Date::now()->subDays($daysAgo)->timestamp;
        $user->save();
    }

    /**
     * Creates a factory user and tracks it for cleanup in tearDown.
     *
     * @param array<string, mixed> $attributes
     */
    private function createTrackedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->createdUserIds[] = $user->id;

        return $user;
    }

    /**
     * When the setting is 0 the feature is disabled and no player is deleted.
     */
    public function testCommandIsDisabledWhenDaysIsZero(): void
    {
        $this->setDeletionDays(0);
        $this->setLastActivityDaysAgo($this->currentUserId, 500);

        // @phpstan-ignore-next-line
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $this->currentUserId]);
    }

    /**
     * A player inactive beyond the threshold is deleted, while a recently active player is kept.
     */
    public function testDeletesInactivePlayerButKeepsActivePlayer(): void
    {
        $this->setDeletionDays(35);

        // Current user (with planets) is inactive.
        $this->setLastActivityDaysAgo($this->currentUserId, 40);

        // A second player that is still active must be preserved.
        $activeUser = $this->createTrackedUser();
        $activeUser->time = (string)Date::now()->timestamp;
        $activeUser->save();

        // @phpstan-ignore-next-line
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $this->currentUserId]);
        $this->assertDatabaseMissing('planets', ['user_id' => $this->currentUserId]);
        $this->assertDatabaseHas('users', ['id' => $activeUser->id]);
    }

    /**
     * Vacation mode does NOT exempt a player from deletion (issue #922 constraint).
     */
    public function testVacationModeDoesNotExemptPlayer(): void
    {
        $this->setDeletionDays(35);

        $user = User::findOrFail($this->currentUserId);
        $user->vacation_mode = true;
        $user->vacation_mode_activated_at = Date::now();
        $user->save();
        $this->setLastActivityDaysAgo($this->currentUserId, 40);

        // @phpstan-ignore-next-line
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $this->currentUserId]);
    }

    /**
     * Admin players are excluded from automatic deletion (mirrors "administrators cannot be banned").
     */
    public function testExcludesAdminPlayers(): void
    {
        $this->setDeletionDays(35);

        $user = User::findOrFail($this->currentUserId);
        $user->assignRole('admin');
        $this->setLastActivityDaysAgo($this->currentUserId, 40);

        // @phpstan-ignore-next-line
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $this->currentUserId]);

        // Clean up the role assignment so the account is not left as an admin.
        $user->removeRole('admin');
    }

    /**
     * Full-wipe integrity: planets, moons and queues are removed and the player's own fleet
     * missions are deleted, while battle/espionage reports are preserved (planet_user_id nulled).
     */
    public function testDeletionAbandonsPlanetsAndPreservesReports(): void
    {
        $this->setDeletionDays(35);

        $userId = $this->currentUserId;
        $mainPlanet = $this->planetService;
        $mainPlanetId = $mainPlanet->getPlanetId();
        $coords = $mainPlanet->getPlanetCoordinates();

        // Give the main planet a moon so moon removal is exercised.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($mainPlanet, 2000000, 20);
        $moonId = $moon->getPlanetId();

        // Create a building queue entry on the main planet.
        $this->planetAddResources(new Resources(10000, 10000, 0, 0));
        $this->addResourceBuildRequest('metal_mine');
        $this->assertDatabaseHas('building_queues', ['planet_id' => $mainPlanetId]);

        // The player's own outgoing fleet mission must be deleted.
        $ownMissionId = DB::table('fleet_missions')->insertGetId([
            'user_id' => $userId,
            'planet_id_from' => $mainPlanetId,
            'mission_type' => 3,
            'time_departure' => Date::now()->timestamp,
            'time_arrival' => Date::now()->addHour()->timestamp,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        // An espionage report about the player must survive (FK is ON DELETE SET NULL).
        $reportId = DB::table('espionage_reports')->insertGetId([
            'planet_galaxy' => $coords->galaxy,
            'planet_system' => $coords->system,
            'planet_position' => $coords->position,
            'planet_type' => PlanetType::Planet->value,
            'planet_user_id' => $userId,
            'player_info' => json_encode(['username' => 'Test']),
            'resources' => json_encode(['metal' => 0, 'crystal' => 0, 'deuterium' => 0]),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        $this->setLastActivityDaysAgo($userId, 40);

        // @phpstan-ignore-next-line
        $this->artisan(self::COMMAND)->assertSuccessful();

        // Account and all planet data are gone.
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertDatabaseMissing('planets', ['id' => $mainPlanetId]);
        $this->assertDatabaseMissing('planets', ['id' => $moonId]);
        $this->assertDatabaseMissing('planets', ['user_id' => $userId]);
        $this->assertDatabaseMissing('building_queues', ['planet_id' => $mainPlanetId]);

        // The player's own fleet mission is deleted.
        $this->assertDatabaseMissing('fleet_missions', ['id' => $ownMissionId]);

        // The espionage report is preserved but its planet_user_id is nulled by the SET NULL FK.
        $this->assertDatabaseHas('espionage_reports', ['id' => $reportId, 'planet_user_id' => null]);
    }
}

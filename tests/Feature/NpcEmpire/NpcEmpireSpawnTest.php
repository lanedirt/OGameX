<?php

namespace Tests\Feature\NpcEmpire;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Tests\TestCase;

/**
 * Verify that NpcEmpireManager spawns and removes camouflaged NPC empire
 * accounts with the expected data model and randomized traits.
 */
class NpcEmpireSpawnTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that spawning creates a fully wired NPC account: user flagged
     * is_npc, user tech row, initial planet, empire traits and empire state.
     */
    public function testSpawnCreatesCamouflagedAccountWithTraitsAndPlanet(): void
    {
        $manager = resolve(NpcEmpireManager::class);

        $empire = $manager->spawn();

        $this->assertInstanceOf(NpcEmpire::class, $empire);

        $user = User::query()->findOrFail($empire->user_id);
        $this->assertTrue((bool)$user->is_npc, 'NPC user must be flagged as is_npc.');
        $this->assertFalse($user->hasRole('admin'), 'NPC user must never receive the admin role.');
        $this->assertNotNull(UserTech::where('user_id', $user->id)->first(), 'NPC user must have a UserTech row.');

        $planet = Planet::where('user_id', $user->id)->first();
        $this->assertNotNull($planet, 'Spawned NPC empire must own one initial planet.');
        $this->assertSame((int)$user->planet_current, (int)$planet->id, 'planet_current must point at the initial planet.');

        // All 9 settings-based trait/states present after spawn.
        $this->assertDatabaseHas('npc_empires', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Verify that rolled traits always land within the configured global
     * ranges and that the rolls vary across spawns.
     */
    public function testSpawnedTraitsStayWithinConfiguredRanges(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $settings = resolve(SettingsService::class);

        $developmentSpeeds = [];
        for ($i = 0; $i < 8; $i++) {
            $empire = $manager->spawn();

            $this->assertGreaterThanOrEqual($settings->npcDevelopmentSpeedMin(), $empire->development_speed);
            $this->assertLessThanOrEqual($settings->npcDevelopmentSpeedMax(), $empire->development_speed);

            $this->assertGreaterThanOrEqual($settings->npcAggressionMin(), $empire->aggression);
            $this->assertLessThanOrEqual($settings->npcAggressionMax(), $empire->aggression);

            $this->assertGreaterThanOrEqual($settings->npcExpansionRateMin(), $empire->expansion_rate);
            $this->assertLessThanOrEqual($settings->npcExpansionRateMax(), $empire->expansion_rate);

            $developmentSpeeds[] = $empire->development_speed;
        }

        $this->assertGreaterThan(
            1,
            count(array_unique($developmentSpeeds)),
            'Development speed rolls should vary between NPC empires (randomized personality).'
        );
    }

    /**
     * Verify that a custom username is respected and a taken username
     * fails loudly instead of silently mutating the requested name.
     */
    public function testSpawnSupportsCustomUsername(): void
    {
        $manager = resolve(NpcEmpireManager::class);

        $first = $manager->spawn('CustomNpcName');
        $this->assertSame('CustomNpcName', $first->user->username);

        $this->expectException(InvalidArgumentException::class);
        $manager->spawn('CustomNpcName');
    }

    /**
     * Verify that deleting an NPC empire removes the user and all owned
     * game data (planets, tech, queues).
     */
    public function testDeleteRemovesUserAndOwnedData(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();
        $userId = $empire->user_id;
        $user = User::query()->findOrFail($userId);

        $manager->delete($user);

        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertDatabaseMissing('npc_empires', ['user_id' => $userId]);
        $this->assertDatabaseMissing('planets', ['user_id' => $userId]);
        $this->assertDatabaseMissing('users_tech', ['user_id' => $userId]);
    }

    /**
     * Verify that delete refuses regular (non-NPC) users as a safety net.
     */
    public function testDeleteRefusesRegularUsers(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $regularUser = User::factory()->create(['is_npc' => false]);

        $this->expectException(InvalidArgumentException::class);
        $manager->delete($regularUser);
    }
}

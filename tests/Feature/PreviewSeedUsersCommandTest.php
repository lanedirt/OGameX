<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Factories\GameMessageFactory;
use OGame\Models\Message;
use OGame\Models\User;
use Tests\TestCase;

class PreviewSeedUsersCommandTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Per-ship debris field harvest storage capacity, mirrored from the real ship
     * object definitions (recycler / reaper) that the fixture is meant to represent.
     *
     * @var array<string, int>
     */
    private const SHIP_CAPACITY = [
        'Recycler' => 20000,
        'Reaper' => 10000,
    ];

    public function test_seed_users_creates_valid_debris_field_harvest_messages(): void
    {
        $this->artisan('ogamex:dev:seed-users')->assertExitCode(0);

        $userIds = User::where('email', 'like', '%@ogamex.dev')->pluck('id');
        $this->assertNotEmpty($userIds, 'Seed command should have created preview test users.');

        $messages = Message::whereIn('user_id', $userIds)
            ->where('key', 'debris_field_harvest')
            ->get();

        $this->assertNotEmpty($messages, 'Seed command should have created at least one debris_field_harvest message.');

        foreach ($messages as $message) {
            $params = $message->params;

            // All params required by the DebrisFieldHarvest message must be present.
            foreach (['to', 'coordinates', 'ship_name', 'ship_amount', 'storage_capacity', 'metal', 'crystal', 'deuterium', 'harvested_metal', 'harvested_crystal', 'harvested_deuterium'] as $param) {
                $this->assertArrayHasKey($param, $params, "Missing param '{$param}' in debris_field_harvest message.");
            }

            // Harvested amounts can never exceed the amount available in the debris field.
            $this->assertLessThanOrEqual((int) $params['metal'], (int) $params['harvested_metal']);
            $this->assertLessThanOrEqual((int) $params['crystal'], (int) $params['harvested_crystal']);
            $this->assertLessThanOrEqual((int) $params['deuterium'], (int) $params['harvested_deuterium']);

            // "to" is wrapped in [debrisfield], "coordinates" keeps [coordinates], matching
            // what RecycleMission/AttackMission send in production.
            $this->assertStringContainsString('[debrisfield]', $params['to']);
            $this->assertStringContainsString('[coordinates]', $params['coordinates']);

            // ship_name must be a known harvesting ship, and storage_capacity must scale
            // with ship_amount using that ship's real cargo capacity.
            $this->assertArrayHasKey($params['ship_name'], self::SHIP_CAPACITY, "Unexpected ship_name '{$params['ship_name']}'.");
            $expectedCapacity = (int) $params['ship_amount'] * self::SHIP_CAPACITY[$params['ship_name']];
            $this->assertEquals($expectedCapacity, (int) $params['storage_capacity']);

            // Rendering the message must not leak any unresolved param placeholders.
            $gameMessage = GameMessageFactory::createGameMessage($message);
            $this->assertStringNotContainsString('?undefined?', $gameMessage->getSubject());
            $this->assertStringNotContainsString('?undefined?', $gameMessage->getBody());
        }
    }
}

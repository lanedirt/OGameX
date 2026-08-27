<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Console\Commands\Dev\PreviewSeedUsers;
use OGame\Factories\GameMessageFactory;
use OGame\Models\Message;
use OGame\Models\User;
use OGame\Services\ObjectService;
use Tests\TestCase;

class PreviewSeedUsersCommandTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Exercises the same template wiring seedMessages() uses (a {key, params} entry fed
     * through createMessages()), so the debris_field_harvest fixture is verified as an
     * actually-persisted, actually-rendered message, not just as a standalone array.
     */
    public function test_debris_field_harvest_template_is_wired_and_renders(): void
    {
        $command = new PreviewSeedUsers();
        $user = User::factory()->create();

        $command->createMessages($user, [$command->getDebrisFieldHarvestTemplate()], 1);

        $message = Message::where('user_id', $user->id)->where('key', 'debris_field_harvest')->first();
        $this->assertNotNull($message, 'createMessages() should have persisted a debris_field_harvest message.');

        $gameMessage = GameMessageFactory::createGameMessage($message);
        $this->assertStringNotContainsString('?undefined?', $gameMessage->getSubject());
        $this->assertStringNotContainsString('?undefined?', $gameMessage->getBody());
    }

    /**
     * Directly exercises the debris_field_harvest fixture generator without running
     * the full ogamex:dev:seed-users command, which also creates 10 users, planets,
     * and unrelated message templates. This keeps the test focused on the fixture's
     * own correctness instead of depending on unrelated seeding to succeed.
     */
    public function test_debris_field_harvest_fixture_produces_valid_messages(): void
    {
        $command = new PreviewSeedUsers();

        for ($i = 0; $i < 50; $i++) {
            $params = $command->generateDebrisFieldHarvestParams();

            // All params required by the DebrisFieldHarvest message must be present.
            foreach (['to', 'coordinates', 'ship_name', 'ship_amount', 'storage_capacity', 'metal', 'crystal', 'deuterium', 'harvested_metal', 'harvested_crystal', 'harvested_deuterium'] as $param) {
                $this->assertArrayHasKey($param, $params, "Missing param '{$param}' in debris_field_harvest message.");
            }

            // Harvested amounts can never exceed the amount available in the debris field.
            $this->assertLessThanOrEqual((int) $params['metal'], (int) $params['harvested_metal']);
            $this->assertLessThanOrEqual((int) $params['crystal'], (int) $params['harvested_crystal']);
            $this->assertLessThanOrEqual((int) $params['deuterium'], (int) $params['harvested_deuterium']);

            // Harvested amounts can never exceed what the ships have room to carry.
            $harvestedTotal = (int) $params['harvested_metal'] + (int) $params['harvested_crystal'] + (int) $params['harvested_deuterium'];
            $this->assertLessThanOrEqual((int) $params['storage_capacity'], $harvestedTotal);

            // "to" is wrapped in [debrisfield], "coordinates" keeps [coordinates], matching
            // what RecycleMission/AttackMission send in production.
            $this->assertStringContainsString('[debrisfield]', $params['to']);
            $this->assertStringContainsString('[coordinates]', $params['coordinates']);

            // ship_name must be a known harvesting ship, and storage_capacity must scale
            // with ship_amount using that ship's real cargo capacity.
            $machineNames = ['recycler', 'reaper', 'pathfinder'];
            $matchedShip = null;
            foreach ($machineNames as $machineName) {
                $ship = ObjectService::getShipObjectByMachineName($machineName);
                if ($ship->title === $params['ship_name']) {
                    $matchedShip = $ship;
                    break;
                }
            }
            if ($matchedShip === null) {
                $this->fail("Unexpected ship_name '{$params['ship_name']}'.");
            }
            $expectedCapacity = (int) $params['ship_amount'] * $matchedShip->properties->capacity->rawValue;
            $this->assertEquals($expectedCapacity, (int) $params['storage_capacity']);

            // Rendering the message must not leak any unresolved param placeholders.
            $message = new Message();
            $message->key = 'debris_field_harvest';
            $message->params = $params;
            $gameMessage = GameMessageFactory::createGameMessage($message);
            $this->assertStringNotContainsString('?undefined?', $gameMessage->getSubject());
            $this->assertStringNotContainsString('?undefined?', $gameMessage->getBody());
        }
    }
}

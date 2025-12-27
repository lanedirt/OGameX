<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ObjectService;
use OGame\Models\User;
use OGame\Services\PlayerService;
use Tests\AccountTestCase;

class LegorAccountTest extends AccountTestCase
{
    use RefreshDatabase;

    /**
     * Test that the Legor initialization command creates the account properly.
     */
    public function testLegorAccountCreation(): void
    {
        // Migration creates Legor, but we can test the moon creation part
        /** @var \Illuminate\Testing\PendingCommand $result */
        $result = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result->assertExitCode(0);

        // Verify user was created by migration
        $legor = User::where('username', 'Legor')->first();
        $this->assertNotNull($legor);
        $this->assertTrue($legor->hasRole('admin'));

        // Verify planet was created at 1:1:2
        $planet = Planet::where('user_id', $legor->id)
            ->where('galaxy', 1)
            ->where('system', 1)
            ->where('planet', 2)
            ->first();
        $this->assertNotNull($planet);
        $this->assertEquals('Arakis', $planet->name);
    }

    /**
     * Test that running the command twice doesn't create duplicate accounts.
     */
    public function testLegorAccountIdempotent(): void
    {
        // Migration creates Legor, command should still work (moon creation)
        /** @var \Illuminate\Testing\PendingCommand $result1 */
        $result1 = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result1->assertExitCode(0);

        /** @var \Illuminate\Testing\PendingCommand $result2 */
        $result2 = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result2->assertExitCode(0);

        // Verify only one Legor user exists
        $legorCount = User::where('username', 'Legor')->count();
        $this->assertEquals(1, $legorCount);
    }

    /**
     * Test that the command fails if position 1:1:2 is already occupied.
     */
    public function testLegorCommandFailsIfPositionOccupied(): void
    {
        // Delete Legor created by migration using raw SQL to handle foreign keys
        DB::table('users')->where('id', 1)->update(['planet_current' => null]);
        DB::table('model_has_roles')->where('model_id', 1)->delete();
        DB::table('users_tech')->where('user_id', 1)->delete();
        DB::table('planets')->where('user_id', 1)->delete();
        DB::table('users')->where('id', 1)->delete();

        // Create a planet at 1:1:2 first
        $planetServiceFactory = app()->make(PlanetServiceFactory::class);
        $playerService = app()->make(PlayerService::class);
        $playerService->load($this->planetService->getPlayer()->getId());

        $coordinate = new Coordinate(1, 1, 2);
        $planetServiceFactory->createPlanetAtPosition($playerService, $coordinate, 'TestPlanet');

        // Run the command - it should fail
        /** @var \Illuminate\Testing\PendingCommand $result */
        $result = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result->assertExitCode(1);
    }

    /**
     * Test that attacks on Legor's planet are blocked.
     */
    public function testLegorPlanetCannotBeAttacked(): void
    {
        // Migration creates Legor, just create moon for testing
        $this->artisan('ogamex:init-legor', ['--delay' => 0]);

        $legor = User::where('username', 'Legor')->first();
        $legorPlanet = Planet::where('user_id', $legor->id)
            ->where('planet_type', 1) // Planet only
            ->first();

        // Add ships to test player's planet
        $unitCollection = new UnitCollection();
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $unitCollection->addUnit($lightFighter, 5);
        $this->planetService->addUnits($unitCollection);

        // Attempt attack - should fail with admin protection message
        $targetCoordinate = new Coordinate(
            $legorPlanet->galaxy,
            $legorPlanet->system,
            $legorPlanet->planet
        );

        // Use the fleet dispatch endpoint
        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $targetCoordinate->galaxy,
            'system' => $targetCoordinate->system,
            'position' => $targetCoordinate->position,
            'type' => 1, // Planet
            'mission' => 1, // Attack
            'speed' => 100,
            'am' . $lightFighter->id => 5,
            'token' => csrf_token(),
        ]);

        // Should fail with admin protection - attack mission should be disabled
        $response->assertStatus(200);
        $responseContent = $response->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        $content = json_decode($responseContent, true);
        $this->assertFalse($content['orders'][1] ?? true, 'Attack mission (type 1) should not be enabled for Legor\'s planet');
    }

    /**
     * Test that espionage on Legor's planet is blocked.
     */
    public function testLegorPlanetCannotBeProbed(): void
    {
        // Migration creates Legor, just create moon for testing
        $this->artisan('ogamex:init-legor', ['--delay' => 0]);

        $legor = User::where('username', 'Legor')->first();
        $legorPlanet = Planet::where('user_id', $legor->id)
            ->where('planet_type', 1) // Planet only
            ->first();

        // Add espionage probes to test player's planet
        $unitCollection = new UnitCollection();
        $espionageProbe = ObjectService::getUnitObjectByMachineName('espionage_probe');
        $unitCollection->addUnit($espionageProbe, 1);
        $this->planetService->addUnits($unitCollection);

        // Attempt espionage - should fail with admin protection message
        $targetCoordinate = new Coordinate(
            $legorPlanet->galaxy,
            $legorPlanet->system,
            $legorPlanet->planet
        );

        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $targetCoordinate->galaxy,
            'system' => $targetCoordinate->system,
            'position' => $targetCoordinate->position,
            'type' => 1, // Planet
            'mission' => 6, // Espionage
            'speed' => 100,
            'am' . $espionageProbe->id => 1,
            'token' => csrf_token(),
        ]);

        // Should fail with admin protection - espionage mission should be disabled
        $response->assertStatus(200);
        $responseContent = $response->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        $content = json_decode($responseContent, true);
        $this->assertFalse($content['orders'][6] ?? true, 'Espionage mission (type 6) should not be enabled for Legor\'s planet');
    }

    /**
     * Test that resources can be sent to Legor's planet (transport should work).
     */
    public function testCanSendResourcesToLegorPlanet(): void
    {
        // Migration creates Legor, just create moon for testing
        $this->artisan('ogamex:init-legor', ['--delay' => 0]);

        $legor = User::where('username', 'Legor')->first();
        $legorPlanet = Planet::where('user_id', $legor->id)
            ->where('planet_type', 1) // Planet only
            ->first();

        // Add small cargo ships to test player's planet
        $unitCollection = new UnitCollection();
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $unitCollection->addUnit($smallCargo, 1);
        $this->planetService->addUnits($unitCollection);

        // Attempt transport - should succeed
        $targetCoordinate = new Coordinate(
            $legorPlanet->galaxy,
            $legorPlanet->system,
            $legorPlanet->planet
        );

        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $targetCoordinate->galaxy,
            'system' => $targetCoordinate->system,
            'position' => $targetCoordinate->position,
            'type' => 1, // Planet
            'mission' => 3, // Transport
            'speed' => 100,
            'am' . $smallCargo->id => 1,
            'metal' => 100,
            'crystal' => 100,
            'deuterium' => 0,
            'token' => csrf_token(),
        ]);

        // Should succeed (transport mission should be enabled)
        $response->assertStatus(200);
        $responseContent = $response->getContent();
        $this->assertNotFalse($responseContent, 'Response content should not be false');
        $content = json_decode($responseContent, true);
        $this->assertTrue($content['orders'][3] ?? false); // Transport mission (type 3) should be enabled
    }

    /**
     * Test that moon is created after the delay.
     */
    public function testMoonIsCreatedAfterDelay(): void
    {
        // Create Legor account with minimal delay for faster test
        /** @var \Illuminate\Testing\PendingCommand $result */
        $result = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result->assertExitCode(0);

        $legor = User::where('username', 'Legor')->first();

        // Moon should be created immediately with 0 delay
        $moon = Planet::where('user_id', $legor->id)
            ->where('planet_type', 3) // moon
            ->first();

        $this->assertNotNull($moon, 'Legor should have a moon after command completes');
        $this->assertEquals('Moon', $moon->name);
    }

    /**
     * Test that debris field is created along with the moon.
     */
    public function testDebrisFieldIsCreated(): void
    {
        // Create Legor account
        /** @var \Illuminate\Testing\PendingCommand $result */
        $result = $this->artisan('ogamex:init-legor', ['--delay' => 0]);
        $result->assertExitCode(0);

        // Check for debris field at 1:1:2
        $debris = \OGame\Models\DebrisField::where('galaxy', 1)
            ->where('system', 1)
            ->where('planet', 2)
            ->first();

        $this->assertNotNull($debris, 'Debris field should exist at 1:1:2');
        $this->assertGreaterThan(30000, $debris->metal + $debris->crystal, 'Debris should be > 30k resources');
    }
}

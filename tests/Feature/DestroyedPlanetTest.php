<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\AttackMission;
use OGame\GameMissions\EspionageMission;
use OGame\GameMissions\TransportMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Tests for soft "Destroyed Planet" abandon behavior (issue #146).
 */
class DestroyedPlanetTest extends AccountTestCase
{
    /**
     * Abandoning a planet soft-flags it instead of hard-deleting the row.
     */
    public function testAbandonSoftFlagsPlanet(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $planetId = $this->secondPlanetService->getPlanetId();
        $startCount = $this->planetService->getPlayer()->planets->planetCount();

        $this->get('/overview?cp=' . $planetId);
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('Planet has been abandoned successfully!', (string) $response->getContent());

        $row = Planet::find($planetId);
        $this->assertNotNull($row, 'Abandoned planet row should still exist');
        $this->assertGreaterThan(0, (int) $row->destroyed);
        $this->assertEquals(0, (int) $row->metal_production);
        $this->assertEquals(0, (int) $row->crystal_production);
        $this->assertEquals(0, (int) $row->deuterium_production);

        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        $this->assertEquals($startCount - 1, $this->planetService->getPlayer()->planets->planetCount());
    }

    /**
     * Galaxy overview marks abandoned planets as destroyed with no click missions.
     */
    public function testGalaxyShowsDestroyedPlanet(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $coords = $this->secondPlanetService->getPlanetCoordinates();
        $this->secondPlanetService->markAsDestroyed();

        $this->get('/');
        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
        ]);
        $response->assertStatus(200);

        $galaxyRows = $response->json('system.galaxyContent');
        $this->assertIsArray($galaxyRows);

        $destroyedRow = null;
        foreach ($galaxyRows as $row) {
            if ((int) ($row['position'] ?? 0) === $coords->position) {
                $destroyedRow = $row;
                break;
            }
        }

        $this->assertNotNull($destroyedRow, 'Destroyed planet should still occupy its galaxy slot');
        $this->assertSame('Deep space', $destroyedRow['playerName'] ?? null);
        $this->assertNotEmpty($destroyedRow['planets'] ?? []);
        $this->assertTrue($destroyedRow['planets'][0]['isDestroyed'] ?? false);
        $this->assertSame(__('t_galaxy.planet.destroyed'), $destroyedRow['planets'][0]['planetName'] ?? null);
        $this->assertSame([], $destroyedRow['planets'][0]['availableMissions'] ?? ['not-empty']);
        $this->assertSame([], $destroyedRow['actions'] ?? ['not-empty']);
    }

    /**
     * Destroyed moons render with moon_c and cannot be targeted by fleets.
     */
    public function testDestroyedMoonIsUntargetableAndUsesRedBorderSprite(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($this->secondPlanetService, 2000000, 20);
        $this->assertNotNull($moon);

        $coords = $this->secondPlanetService->getPlanetCoordinates();
        $this->secondPlanetService->markAsDestroyed();

        $moonRow = Planet::find($moon->getPlanetId());
        $this->assertNotNull($moonRow);
        $this->assertGreaterThan(0, (int) $moonRow->destroyed);

        $this->get('/');
        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
        ]);
        $response->assertStatus(200);

        $galaxyRows = $response->json('system.galaxyContent');
        $slot = null;
        foreach ($galaxyRows as $row) {
            if ((int) ($row['position'] ?? 0) === $coords->position) {
                $slot = $row;
                break;
            }
        }
        $this->assertNotNull($slot);

        $moonPayload = null;
        foreach ($slot['planets'] as $body) {
            if ((int) ($body['planetType'] ?? 0) === PlanetType::Moon->value) {
                $moonPayload = $body;
                break;
            }
        }
        $this->assertNotNull($moonPayload);
        $this->assertTrue($moonPayload['isDestroyed']);
        $this->assertSame('moon_c', $moonPayload['imageInformation']);

        $attackMission = resolve(AttackMission::class);
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);

        $status = $attackMission->isMissionPossible(
            $this->planetService,
            $coords,
            PlanetType::Moon,
            $units
        );
        $this->assertFalse($status->possible, 'Destroyed moons must not be attackable');
    }

    /**
     * Attack and espionage remain possible against destroyed planets; transport does not.
     */
    public function testAttackAndEspionageAllowedOnDestroyedPlanet(): void
    {
        $foreignPlanet = $this->getNearbyForeignCleanPlanet();
        $foreignPlanet->addResources(new Resources(1000, 1000, 1000, 0));
        $coords = $foreignPlanet->getPlanetCoordinates();

        // Soft-flag without last-planet guard (clean planet is not the owner's only planet).
        $foreignPlanet->applyDestroyedFlag((int) Date::now()->timestamp);

        $attackMission = resolve(AttackMission::class);
        $espionageMission = resolve(EspionageMission::class);
        $transportMission = resolve(TransportMission::class);

        $fighters = new UnitCollection();
        $fighters->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);

        $probes = new UnitCollection();
        $probes->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);

        $this->assertTrue(
            $attackMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $fighters)->possible
        );
        $this->assertTrue(
            $espionageMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $probes)->possible
        );
        $this->assertFalse(
            $transportMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $fighters)->possible
        );
    }

    /**
     * Daily purge permanently deletes bodies flagged for at least 24 hours.
     */
    public function testCleanupDestroyedPlanetsPurgesAfter24Hours(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $planetId = $this->secondPlanetService->getPlanetId();

        // Flag as destroyed 25 hours ago so it qualifies for the 3:00 purge window.
        $this->secondPlanetService->applyDestroyedFlag((int) Date::now()->subHours(25)->timestamp);

        $this->assertDatabaseHas('planets', ['id' => $planetId]);

        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')
            ->assertSuccessful();

        $this->assertDatabaseMissing('planets', ['id' => $planetId]);
    }

    /**
     * Bodies destroyed less than 24 hours ago are kept by the purge job.
     */
    public function testCleanupDestroyedPlanetsKeepsRecentFlags(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $planetId = $this->secondPlanetService->getPlanetId();

        $this->secondPlanetService->applyDestroyedFlag((int) Date::now()->subHours(1)->timestamp);

        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')
            ->assertSuccessful();

        $this->assertDatabaseHas('planets', [
            'id' => $planetId,
        ]);
        $this->assertGreaterThan(0, (int) Planet::find($planetId)->destroyed);
    }
}

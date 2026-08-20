<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\AttackMission;
use OGame\GameMissions\ColonisationMission;
use OGame\GameMissions\DeploymentMission;
use OGame\GameMissions\EspionageMission;
use OGame\GameMissions\TransportMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use RuntimeException;
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
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }
        $startCount = $player->planets->planetCount();

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

        $player->load($player->getId());
        $this->assertEquals($startCount - 1, $player->planets->planetCount());
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
        $secondPlanet = $this->secondPlanetService;
        if ($secondPlanet === null) {
            $this->fail('Second planet is null.');
        }
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($secondPlanet, 2000000, 20);
        $this->assertNotNull($moon);

        $coords = $secondPlanet->getPlanetCoordinates();
        $secondPlanet->markAsDestroyed();

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
     * Attack, espionage and transport remain possible against destroyed planets.
     */
    public function testAttackEspionageAndTransportAllowedOnDestroyedPlanet(): void
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

        $cargo = new UnitCollection();
        $cargo->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $this->assertTrue(
            $attackMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $fighters)->possible
        );
        $this->assertTrue(
            $espionageMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $probes)->possible
        );
        $this->assertTrue(
            $transportMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $cargo)->possible
        );
    }

    /**
     * Defense left on a destroyed planet stays and fights when attacked.
     */
    public function testAttackFightsDefenseOnDestroyedPlanet(): void
    {
        $foreignPlanet = $this->getNearbyForeignCleanPlanet();
        $foreignPlanet->addUnit('rocket_launcher', 50);
        $foreignPlanet->addResources(new Resources(10000, 10000, 10000, 0));
        $this->assertSame(50, $foreignPlanet->getObjectAmount('rocket_launcher'));

        $coords = $foreignPlanet->getPlanetCoordinates();
        $foreignPlanet->applyDestroyedFlag((int) Date::now()->timestamp);
        $this->assertSame(50, $foreignPlanet->getObjectAmount('rocket_launcher'), 'Defense must remain after soft-delete');

        $this->planetAddUnit('light_fighter', 200);
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 200);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $fleetMission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $coords,
            PlanetType::Planet,
            1,
            $units,
            new Resources(0, 0, 0, 0),
            10
        );

        $duration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $coords,
            $units,
            resolve(AttackMission::class)
        );
        $this->travel($duration + 1)->seconds();
        $this->reloadApplication();

        $response = $this->get('/overview');
        $response->assertStatus(200);

        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMission->id, false);
        $this->assertNotNull($fleetMission);
        $this->assertSame(1, (int) $fleetMission->processed, 'Attack against destroyed planet should process');

        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should be created against destroyed planet defense');
    }

    /**
     * Deployment is blocked; colonisation is blocked while the destroyed row occupies the slot.
     */
    public function testDeploymentAndColonisationBlockedOnDestroyedPlanet(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $coords = $this->secondPlanetService->getPlanetCoordinates();
        $this->secondPlanetService->applyDestroyedFlag((int) Date::now()->timestamp);

        $cargo = new UnitCollection();
        $cargo->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $colonyShips = new UnitCollection();
        $colonyShips->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);

        $deploymentMission = resolve(DeploymentMission::class);
        $colonisationMission = resolve(ColonisationMission::class);

        $this->assertFalse(
            $deploymentMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $cargo)->possible,
            'Deployment must be blocked to destroyed planets'
        );
        $this->assertFalse(
            $colonisationMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $colonyShips)->possible,
            'Colonisation must stay blocked while destroyed planet occupies the slot'
        );
    }

    /**
     * Destroyed planets do not accrue resources even if production rates are non-zero.
     */
    public function testDestroyedPlanetDoesNotAccrueResources(): void
    {
        $this->assertNotNull($this->secondPlanetService);
        $planet = $this->secondPlanetService;
        $planet->applyDestroyedFlag((int) Date::now()->timestamp);

        // Simulate stale non-zero rates that could race with soft-delete.
        $row = Planet::find($planet->getPlanetId());
        $this->assertNotNull($row);
        $row->metal_production = 1000;
        $row->crystal_production = 1000;
        $row->deuterium_production = 1000;
        $row->save();
        $planet->reloadPlanet();

        $metalBefore = (int) $planet->metal()->get();
        $until = (int) Date::now()->addHour()->timestamp;
        $planet->updateResourcesUntil($until);

        $this->assertSame($metalBefore, (int) $planet->metal()->get(), 'Destroyed planets must not accrue resources');
        $row->refresh();
        $this->assertSame($until, (int) $row->time_last_update);
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

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')->assertSuccessful();

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

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')->assertSuccessful();

        $this->assertDatabaseHas('planets', [
            'id' => $planetId,
        ]);
        $keptPlanet = Planet::find($planetId);
        $this->assertNotNull($keptPlanet);
        $this->assertGreaterThan(0, (int) $keptPlanet->destroyed);
    }

    /**
     * Purge teleports an in-flight return mission home before deleting the body.
     */
    public function testCleanupTeleportsInFlightReturnMission(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }

        $homePlanet = $this->planetService;
        $abandonedPlanet = $this->secondPlanetService;
        $homeId = $homePlanet->getPlanetId();
        $abandonedId = $abandonedPlanet->getPlanetId();
        $homeCoords = $homePlanet->getPlanetCoordinates();
        $abandonedCoords = $abandonedPlanet->getPlanetCoordinates();

        $shipsBefore = $homePlanet->getObjectAmount('small_cargo');
        $metalBefore = (int) $homePlanet->metal()->get();

        $parent = new FleetMission();
        $parent->user_id = $player->getId();
        $parent->planet_id_from = $homeId;
        $parent->planet_id_to = $abandonedId;
        $parent->galaxy_from = $homeCoords->galaxy;
        $parent->system_from = $homeCoords->system;
        $parent->position_from = $homeCoords->position;
        $parent->galaxy_to = $abandonedCoords->galaxy;
        $parent->system_to = $abandonedCoords->system;
        $parent->position_to = $abandonedCoords->position;
        $parent->type_from = PlanetType::Planet->value;
        $parent->type_to = PlanetType::Planet->value;
        $parent->mission_type = 3;
        $parent->time_departure = (int) Date::now()->subHours(2)->timestamp;
        $parent->time_arrival = (int) Date::now()->subHour()->timestamp;
        $parent->processed = 1;
        $parent->processed_hold = 0;
        $parent->canceled = 0;
        $parent->metal = 0;
        $parent->crystal = 0;
        $parent->deuterium = 0;
        $parent->deuterium_consumption = 0;
        $parent->small_cargo = 0;
        $parent->save();

        $returnMission = new FleetMission();
        $returnMission->user_id = $player->getId();
        $returnMission->parent_id = $parent->id;
        $returnMission->planet_id_from = $abandonedId;
        $returnMission->planet_id_to = $homeId;
        $returnMission->galaxy_from = $abandonedCoords->galaxy;
        $returnMission->system_from = $abandonedCoords->system;
        $returnMission->position_from = $abandonedCoords->position;
        $returnMission->galaxy_to = $homeCoords->galaxy;
        $returnMission->system_to = $homeCoords->system;
        $returnMission->position_to = $homeCoords->position;
        $returnMission->type_from = PlanetType::Planet->value;
        $returnMission->type_to = PlanetType::Planet->value;
        $returnMission->mission_type = 3;
        $returnMission->time_departure = (int) Date::now()->subMinutes(30)->timestamp;
        $returnMission->time_arrival = (int) Date::now()->addHour()->timestamp;
        $returnMission->processed = 0;
        $returnMission->processed_hold = 0;
        $returnMission->canceled = 0;
        $returnMission->metal = 500;
        $returnMission->crystal = 0;
        $returnMission->deuterium = 0;
        $returnMission->deuterium_consumption = 0;
        $returnMission->small_cargo = 3;
        $returnMission->save();

        $abandonedPlanet->applyDestroyedFlag((int) Date::now()->subHours(25)->timestamp);

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')->assertSuccessful();

        $this->assertDatabaseMissing('planets', ['id' => $abandonedId]);

        $returnMission->refresh();
        $this->assertSame(1, (int) $returnMission->processed, 'Return mission should be force-processed during purge');

        $homePlanet->reloadPlanet();
        $this->assertSame($shipsBefore + 3, $homePlanet->getObjectAmount('small_cargo'));
        $this->assertSame($metalBefore + 500, (int) $homePlanet->metal()->get());
    }

    /**
     * Purge recalls an in-flight outbound mission targeting the body before deleting it.
     */
    public function testCleanupRecallsOutboundMissionToDestroyedPlanet(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }

        $homePlanet = $this->planetService;
        $abandonedPlanet = $this->secondPlanetService;
        $abandonedId = $abandonedPlanet->getPlanetId();
        $abandonedCoords = $abandonedPlanet->getPlanetCoordinates();

        $homePlanet->addUnit('small_cargo', 5);
        $homePlanet->addResources(new Resources(0, 0, 100000, 0));
        $shipsBeforeDispatch = $homePlanet->getObjectAmount('small_cargo');

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 3);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $outbound = $fleetMissionService->createNewFromPlanet(
            $homePlanet,
            $abandonedCoords,
            PlanetType::Planet,
            3,
            $units,
            new Resources(0, 0, 0, 0),
            10
        );

        $homePlanet->reloadPlanet();
        $this->assertSame($shipsBeforeDispatch - 3, $homePlanet->getObjectAmount('small_cargo'));

        // Soft-flag after dispatch: abandon normally blocks active fleets, but attackers/transports
        // can still be in flight when the daily purge runs.
        $abandonedPlanet->applyDestroyedFlag((int) Date::now()->subHours(25)->timestamp);

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')->assertSuccessful();

        $this->assertDatabaseMissing('planets', ['id' => $abandonedId]);

        $outbound->refresh();
        $this->assertSame(1, (int) $outbound->canceled, 'Outbound mission should be recalled during purge');
        $this->assertSame(1, (int) $outbound->processed);

        $returnMission = FleetMission::where('parent_id', $outbound->id)->first();
        $this->assertNotNull($returnMission, 'Recall should create a return mission with the ships');
        $this->assertSame(3, (int) $returnMission->small_cargo);
    }

    /**
     * Moon-origin fleets must survive purge when the parent planet is also destroyed.
     * Rebinding to the dying parent would let the parent's teleport cancel the ships.
     */
    public function testCleanupPreservesMoonOriginFleetWhenParentAlsoPurged(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }

        $homePlanet = $this->planetService;
        $abandonedPlanet = $this->secondPlanetService;
        $homeId = $homePlanet->getPlanetId();
        $abandonedId = $abandonedPlanet->getPlanetId();

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($abandonedPlanet, 2000000, 20);
        $this->assertNotNull($moon);
        $moonId = $moon->getPlanetId();
        $moonCoords = $moon->getPlanetCoordinates();

        // Empty slot for a recycle-like outbound destination (coordinates only; no target body).
        $targetCoords = $homePlanet->getPlanetCoordinates();

        $outbound = new FleetMission();
        $outbound->user_id = $player->getId();
        $outbound->planet_id_from = $moonId;
        $outbound->planet_id_to = null;
        $outbound->galaxy_from = $moonCoords->galaxy;
        $outbound->system_from = $moonCoords->system;
        $outbound->position_from = $moonCoords->position;
        $outbound->galaxy_to = $targetCoords->galaxy;
        $outbound->system_to = $targetCoords->system;
        $outbound->position_to = min(15, $targetCoords->position + 1);
        $outbound->type_from = PlanetType::Moon->value;
        $outbound->type_to = PlanetType::Planet->value;
        $outbound->mission_type = 3;
        $outbound->time_departure = (int) Date::now()->subMinutes(10)->timestamp;
        $outbound->time_arrival = (int) Date::now()->addHour()->timestamp;
        $outbound->processed = 0;
        $outbound->processed_hold = 0;
        $outbound->canceled = 0;
        $outbound->metal = 0;
        $outbound->crystal = 0;
        $outbound->deuterium = 0;
        $outbound->deuterium_consumption = 0;
        $outbound->small_cargo = 4;
        $outbound->save();

        // Bypass abandon guards: soft-flag both bodies as already past the 24h grace window.
        $destroyedAt = (int) Date::now()->subHours(25)->timestamp;
        $abandonedPlanet->applyDestroyedFlag($destroyedAt);
        $moon->applyDestroyedFlag($destroyedAt);

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:cleanup-destroyed-planets')->assertSuccessful();

        $this->assertDatabaseMissing('planets', ['id' => $abandonedId]);
        $this->assertDatabaseMissing('planets', ['id' => $moonId]);

        $outbound->refresh();
        $this->assertSame(0, (int) $outbound->canceled, 'Moon-origin fleet must not be canceled during parent+moon purge');
        $this->assertSame(0, (int) $outbound->processed);
        $this->assertSame($homeId, (int) $outbound->planet_id_from, 'Fleet must rebind to a living colony, not the dying parent');
        $this->assertSame(4, (int) $outbound->small_cargo);
    }

    /**
     * Former owner's vacation mode must not protect a destroyed planet (Deep space).
     */
    public function testVacationModeDoesNotProtectDestroyedPlanet(): void
    {
        $foreignPlanet = $this->getNearbyForeignCleanPlanet();
        $coords = $foreignPlanet->getPlanetCoordinates();

        $formerOwner = $foreignPlanet->getPlayer();
        if ($formerOwner === null) {
            $this->fail('Foreign planet owner is null.');
        }
        $formerOwner->activateVacationMode();
        $this->assertTrue($formerOwner->isInVacationMode());

        $foreignPlanet->applyDestroyedFlag((int) Date::now()->timestamp);

        $attackMission = resolve(AttackMission::class);
        $espionageMission = resolve(EspionageMission::class);
        $transportMission = resolve(TransportMission::class);

        $fighters = new UnitCollection();
        $fighters->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);

        $probes = new UnitCollection();
        $probes->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);

        $cargo = new UnitCollection();
        $cargo->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $this->assertTrue(
            $attackMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $fighters)->possible,
            'Attack must remain possible against destroyed planet whose former owner is in vacation'
        );
        $this->assertTrue(
            $espionageMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $probes)->possible,
            'Espionage must remain possible against destroyed planet whose former owner is in vacation'
        );
        $this->assertTrue(
            $transportMission->isMissionPossible($this->planetService, $coords, PlanetType::Planet, $cargo)->possible,
            'Transport must remain possible against destroyed planet whose former owner is in vacation'
        );
    }

    /**
     * Incoming foreign fleets must not block abandon; own outbound (including moon) must.
     */
    public function testAbandonBlocksOnlyOwnOutboundFleets(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }

        $abandonedPlanet = $this->secondPlanetService;
        $abandonedId = $abandonedPlanet->getPlanetId();
        $abandonedCoords = $abandonedPlanet->getPlanetCoordinates();

        $foreignPlanet = $this->getNearbyForeignCleanPlanet();
        $foreignPlayer = $foreignPlanet->getPlayer();
        if ($foreignPlayer === null) {
            $this->fail('Foreign player is null.');
        }
        $foreignCoords = $foreignPlanet->getPlanetCoordinates();

        // Incoming foreign attack targeting the planet about to be abandoned.
        $incoming = new FleetMission();
        $incoming->user_id = $foreignPlayer->getId();
        $incoming->planet_id_from = $foreignPlanet->getPlanetId();
        $incoming->planet_id_to = $abandonedId;
        $incoming->galaxy_from = $foreignCoords->galaxy;
        $incoming->system_from = $foreignCoords->system;
        $incoming->position_from = $foreignCoords->position;
        $incoming->galaxy_to = $abandonedCoords->galaxy;
        $incoming->system_to = $abandonedCoords->system;
        $incoming->position_to = $abandonedCoords->position;
        $incoming->type_from = PlanetType::Planet->value;
        $incoming->type_to = PlanetType::Planet->value;
        $incoming->mission_type = 1;
        $incoming->time_departure = (int) Date::now()->subMinutes(5)->timestamp;
        $incoming->time_arrival = (int) Date::now()->addHour()->timestamp;
        $incoming->processed = 0;
        $incoming->processed_hold = 0;
        $incoming->canceled = 0;
        $incoming->metal = 0;
        $incoming->crystal = 0;
        $incoming->deuterium = 0;
        $incoming->deuterium_consumption = 0;
        $incoming->light_fighter = 1;
        $incoming->save();

        // Incoming alone must not block abandon.
        $abandonedPlanet->markAsDestroyed();
        $this->assertTrue($abandonedPlanet->isDestroyed());

        // Reset for outbound / moon cases: clear destroyed flag via fresh second planet path.
        // Use a new moon+outbound on the home planet's second body after recreating state.
        $row = Planet::find($abandonedId);
        $this->assertNotNull($row);
        $row->destroyed = 0;
        $row->save();
        $abandonedPlanet->reloadPlanet();
        $player->load($player->getId());

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($abandonedPlanet, 2000000, 20);
        $this->assertNotNull($moon);
        $moonCoords = $moon->getPlanetCoordinates();

        $moonOutbound = new FleetMission();
        $moonOutbound->user_id = $player->getId();
        $moonOutbound->planet_id_from = $moon->getPlanetId();
        $moonOutbound->planet_id_to = $foreignPlanet->getPlanetId();
        $moonOutbound->galaxy_from = $moonCoords->galaxy;
        $moonOutbound->system_from = $moonCoords->system;
        $moonOutbound->position_from = $moonCoords->position;
        $moonOutbound->galaxy_to = $foreignCoords->galaxy;
        $moonOutbound->system_to = $foreignCoords->system;
        $moonOutbound->position_to = $foreignCoords->position;
        $moonOutbound->type_from = PlanetType::Moon->value;
        $moonOutbound->type_to = PlanetType::Planet->value;
        $moonOutbound->mission_type = 3;
        $moonOutbound->time_departure = (int) Date::now()->subMinutes(5)->timestamp;
        $moonOutbound->time_arrival = (int) Date::now()->addHour()->timestamp;
        $moonOutbound->processed = 0;
        $moonOutbound->processed_hold = 0;
        $moonOutbound->canceled = 0;
        $moonOutbound->metal = 0;
        $moonOutbound->crystal = 0;
        $moonOutbound->deuterium = 0;
        $moonOutbound->deuterium_consumption = 0;
        $moonOutbound->small_cargo = 1;
        $moonOutbound->save();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot abandon planet with active fleet missions.');
        $abandonedPlanet->markAsDestroyed();
    }
}

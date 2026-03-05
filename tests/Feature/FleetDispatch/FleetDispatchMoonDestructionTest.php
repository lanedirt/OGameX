<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameMissions\MoonDestructionMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for moon destruction missions (type 9).
 */
class FleetDispatchMoonDestructionTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 9;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Moon Destruction';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        // Give the player 10 Deathstars to guarantee 100% moon destruction chance
        // with diameter=1: (100 - sqrt(1)) * sqrt(10) ≈ 315%, capped at 100%.
        $this->planetAddUnit('deathstar', 10);

        // Ensure enough computer tech for fleets.
        $this->playerSetResearchLevel('computer_technology', 5);

        // Set fleet and economy speed to 1x to simplify timing.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 1);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);

        // Add sufficient deuterium so fuel is never a constraint.
        $this->planetAddResources(new Resources(0, 0, 1_000_000, 0));
    }

    /**
     * For moon destruction we do not assert on specific frontend messages here.
     */
    protected function messageCheckMissionArrival(): void
    {
        // Intentionally left blank for this test.
    }

    /**
     * For moon destruction we do not assert on specific frontend messages here.
     */
    protected function messageCheckMissionReturn(): void
    {
        // Intentionally left blank for this test.
    }

    /**
     * Assert that a moon destruction mission successfully destroys a foreign moon.
     *
     * We make the outcome deterministic by setting the target moon diameter to 1 and sending
     * 10 Deathstars, giving a 100% destruction chance
     * (formula: (100 - sqrt(diameter)) * sqrt(DS) => 99 * sqrt(10) ≈ 315%, capped at 100%).
     */
    public function testMoonDestructionSuccess(): void
    {
        $this->basicSetup();

        // Send moon destruction mission to a nearby foreign moon.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('deathstar'), 10);
        $foreignMoon = $this->sendMissionToOtherPlayerMoon($unitCollection, new Resources(0, 0, 0, 0));

        // Set the target moon diameter to 1 so destruction is guaranteed.
        Planet::where('id', $foreignMoon->getPlanetId())->update(['diameter' => 1]);

        // Calculate fleet travel time.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignMoon->getPlanetCoordinates(),
            $unitCollection,
            resolve(MoonDestructionMission::class)
        );

        // Advance time past fleet arrival.
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to prevent cached state from affecting mission processing.
        $this->reloadApplication();

        // Trigger fleet processing via overview.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert the foreign moon was destroyed.
        $this->assertFalse(
            Planet::where('id', $foreignMoon->getPlanetId())->exists(),
            'Moon should be destroyed after successful moon destruction mission.'
        );
    }

    /**
     * Assert that moon destruction works correctly even when the target moon has active
     * incoming and outgoing fleet missions at the time of destruction.
     *
     * Incoming missions are redirected to the parent planet; outgoing missions have their
     * planet_id_from nulled out. Neither should prevent the moon from being destroyed.
     */
    public function testMoonDestructionSuccessWithActiveIncomingAndOutgoingMissions(): void
    {
        $this->basicSetup();

        // Add small cargo so we can dispatch an incoming transport to the foreign moon.
        $this->planetAddUnit('small_cargo', 5);

        // Dispatch a transport TO the foreign moon. This creates an active incoming mission
        // on the moon and also gives us a reference to which moon to target.
        $this->missionType = 3;
        $transportUnits = new UnitCollection();
        $transportUnits->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $foreignMoon = $this->sendMissionToOtherPlayerMoon($transportUnits, new Resources(0, 0, 0, 0));

        // Set moon diameter to 1 so destruction is guaranteed.
        Planet::where('id', $foreignMoon->getPlanetId())->update(['diameter' => 1]);
        $foreignMoon->reloadPlanet();

        // Create an outgoing mission FROM the foreign moon back to the current player's planet.
        // This tests that moon destruction handles active outgoing missions gracefully.
        $foreignMoon->addUnit('small_cargo', 1);
        $foreignMoon->addResources(new Resources(0, 0, 100_000, 0));
        $outgoingUnits = new UnitCollection();
        $outgoingUnits->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $foreignMoonMissionService = resolve(FleetMissionService::class, ['player' => $foreignMoon->getPlayer()]);
        $foreignMoonMissionService->createNewFromPlanet(
            $foreignMoon,
            $this->planetService->getPlanetCoordinates(),
            PlanetType::Planet,
            3,
            $outgoingUnits,
            new Resources(0, 0, 0, 0),
            10
        );

        // Now dispatch moon destruction to the same foreign moon.
        $this->missionType = 9;
        $destroyUnits = new UnitCollection();
        $destroyUnits->addUnit(ObjectService::getUnitObjectByMachineName('deathstar'), 10);
        $this->dispatchFleet($foreignMoon->getPlanetCoordinates(), $destroyUnits, new Resources(0, 0, 0, 0), PlanetType::Moon);

        // Calculate travel time for the moon destruction fleet.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignMoon->getPlanetCoordinates(),
            $destroyUnits,
            resolve(MoonDestructionMission::class)
        );

        // Advance time past moon destruction arrival.
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to prevent cached state from affecting mission processing.
        $this->reloadApplication();

        // Trigger fleet processing via overview.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert the moon was destroyed despite having active incoming and outgoing missions.
        $this->assertFalse(
            Planet::where('id', $foreignMoon->getPlanetId())->exists(),
            'Moon should be destroyed even when it has active incoming and outgoing fleet missions.'
        );
    }
}

<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameMissions\MoonDestructionMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
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

        $foreignMoon->removeUnits($foreignMoon->getShipUnits(), false);
        $foreignMoon->removeUnits($foreignMoon->getDefenseUnits(), false);
        $foreignMoon->save();
        $foreignMoon->reloadPlanet();

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

        $foreignMoon->removeUnits($foreignMoon->getShipUnits(), false);
        $foreignMoon->removeUnits($foreignMoon->getDefenseUnits(), false);
        $foreignMoon->save();
        $foreignMoon->reloadPlanet();

        // Set moon diameter to 1 so destruction is guaranteed.
        Planet::where('id', $foreignMoon->getPlanetId())->update(['diameter' => 1]);
        $foreignMoon->reloadPlanet();

        // Create an outgoing mission FROM the foreign moon back to the current player's planet.
        // This tests that moon destruction handles active outgoing missions gracefully.
        $foreignMoon->addUnit('small_cargo', 1);
        $foreignMoon->addResources(new Resources(0, 0, 100_000, 0));

        // Boost the foreign player's computer tech to guarantee enough fleet slots, even if
        // previous tests in the suite left unprocessed fleet missions for this player in the DB.
        $foreignMoon->getPlayer()->setResearchLevel('computer_technology', 25);

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

    /**
     * Clean up settings that were mutated for the draw test.
     */
    protected function tearDown(): void
    {
        // Reset the battle engine to the default so it does not bleed into other test classes.
        resolve(SettingsService::class)->set('battle_engine', 'rust');
        parent::tearDown();
    }

    /**
     * Assert that a drawn battle (both sides survive) does not trigger moon destruction.
     *
     * Draw setup
     * ----------
     * Attacker:  2 Deathstars (attack 200 000, shield 50 000, hull 9 000 000)
     * Defender:  1 000 000 Light Fighters (attack 50, shield 10, hull 4 000)
     *
     * Why this is a guaranteed draw in the PHP engine with defender weapon_tech = 0:
     *   • LF attack (50) < 1 % of DS shield (500) → ALL LF attacks are bounced;
     *     the DS cannot receive any hull damage regardless of how many LF there are.
     *   • DS has rapidfire 200 against LF, so each DS fires ~200 shots/round.
     *     2 DS × 200 shots × 6 rounds ≈ 2 400 LF killed — far fewer than 1 000 000.
     *   → After 6 rounds, both sides still have surviving units → draw.
     */
    public function testMoonDestructionDoesNotTriggerAfterDrawnBattle(): void
    {
        $this->basicSetup();

        // Use the PHP battle engine; its deterministic mechanics are easier to reason about.
        resolve(SettingsService::class)->set('battle_engine', 'php');

        // --- Attacker setup ---
        $attacker = $this->planetService;
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();
        $attacker->addUnit('deathstar', 2);
        $attacker->save();

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('deathstar'), 2);

        $foreignMoon = $this->sendMissionToOtherPlayerMoon($units, new Resources(0, 0, 0, 0));

        // Snapshot the outbound mission ID so we can locate it (and its return child) precisely.
        $outboundMission = FleetMission::where('user_id', $attacker->getPlayer()->getId())
            ->where('mission_type', 9)
            ->whereNull('parent_id')
            ->where('processed', 0)
            ->orderBy('id', 'desc')
            ->firstOrFail();
        $outboundMissionId = $outboundMission->id;

        // --- Defender setup: 1 000 000 LF, weapon_tech forced to 0 ---
        $foreignMoon->removeUnits($foreignMoon->getShipUnits(), false);
        $foreignMoon->removeUnits($foreignMoon->getDefenseUnits(), false);
        $foreignMoon->addUnit('light_fighter', 1_000_000, false);
        $foreignMoon->save();
        $foreignMoon->reloadPlanet();

        $defenderPlayer = $foreignMoon->getPlayer();
        $defenderPlayerId = $defenderPlayer->getId();
        // Force weapon tech to 0 so LF attack (50) stays below the 1%-of-shield bounce
        // threshold (500) for the death star — DS remain untouchable.
        $defenderPlayer->setResearchLevel('weapon_technology', 0);

        // --- Calculate travel time and snapshot max message ID ---
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $attacker,
            $foreignMoon->getPlanetCoordinates(),
            $units,
            resolve(MoonDestructionMission::class)
        );

        // Record the highest message ID currently in the DB so we can filter to only
        // messages created during this test — no pollution from earlier tests.
        $maxMessageIdBefore = Message::max('id') ?? 0;

        // --- Process the outbound mission ---
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Moon must still exist (draw → no destruction attempt).
        $this->assertTrue(
            Planet::where('id', $foreignMoon->getPlanetId())->exists(),
            'Moon should remain after a drawn battle.'
        );

        // No moon-destruction follow-up messages should have been created.
        $this->assertSame(
            0,
            Message::where('id', '>', $maxMessageIdBefore)
                ->where('user_id', $attacker->getPlayer()->getId())
                ->whereIn('key', [
                    'moon_destruction_success',
                    'moon_destruction_failure',
                    'moon_destruction_catastrophic',
                    'moon_destruction_mission_failed',
                ])
                ->count(),
            'Attacker should not receive a moon-destruction follow-up message after a draw.'
        );

        $this->assertSame(
            0,
            Message::where('id', '>', $maxMessageIdBefore)
                ->where('user_id', $defenderPlayerId)
                ->whereIn('key', ['moon_destruction_repelled', 'moon_destroyed'])
                ->count(),
            'Defender should not receive a moon-destruction follow-up message after a draw.'
        );

        // A return mission must exist for the two surviving death stars.
        $returnMission = FleetMission::where('parent_id', $outboundMissionId)->first();
        $this->assertNotNull($returnMission, 'A return fleet mission should have been created for surviving death stars.');
        $this->assertSame(2, $returnMission->deathstar, 'Return mission should carry 2 death stars.');

        // --- Process the return mission and verify the DS arrive home ---
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        $attacker->reloadPlanet();
        $this->assertSame(
            2,
            $attacker->getShipUnits()->getAmountByMachineName('deathstar'),
            'Surviving death stars should return home after a drawn battle.'
        );
    }
}

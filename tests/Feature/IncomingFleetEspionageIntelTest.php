<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Tests that foreign incoming fleet details in the event list are limited
 * by the viewing player's Espionage Technology level (issue #668).
 */
class IncomingFleetEspionageIntelTest extends FleetDispatchTestCase
{
    protected int $missionType = 1;

    protected string $missionName = 'Attack';

    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 20);
        $this->playerSetResearchLevel('computer_technology', 1);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
        $settingsService->set('attack_block_until', 0);

        $this->planetAddResources(new Resources(0, 0, 1000000, 0));
    }

    protected function tearDown(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('attack_block_until', 0);
        parent::tearDown();
    }

    /**
     * At espionage 0, defender sees the attack but no ship count or composition.
     */
    public function testEspionageLevelZeroHidesFleetComposition(): void
    {
        $response = $this->fetchDefenderEventListWithIncomingAttack(0, 13);

        $response->assertStatus(200);
        $response->assertSee('Attack');
        $response->assertDontSee('Light Fighter');
        $response->assertDontSee('Shipment');
        $content = $response->getContent() ?: '';
        $this->assertStringNotContainsString('class=&quot;fleetinfo&quot;', $content);
        // detailsFleet span should be empty (no total count)
        $this->assertMatchesRegularExpression('/class="detailsFleet">\s*<span><\/span>/', $content);
    }

    /**
     * At espionage 2, defender sees total ship count but not ship types/amounts.
     */
    public function testEspionageLevelTwoShowsTotalCountOnly(): void
    {
        $response = $this->fetchDefenderEventListWithIncomingAttack(2, 13);

        $response->assertStatus(200);
        $response->assertSee('Attack');
        $content = $response->getContent() ?: '';
        $this->assertMatchesRegularExpression('/class="detailsFleet">\s*<span>13<\/span>/', $content);
        $response->assertDontSee('Light Fighter');
        $response->assertDontSee('Shipment');
    }

    /**
     * At espionage 4, defender sees ship types with "?" amounts, not real counts.
     */
    public function testEspionageLevelFourShowsShipTypesWithoutAmounts(): void
    {
        $response = $this->fetchDefenderEventListWithIncomingAttack(4, 13);

        $response->assertStatus(200);
        $response->assertSee('Attack');
        $response->assertSee('Light Fighter');
        // Tooltip HTML is entity-encoded inside the title attribute
        $response->assertSee('class=&quot;value&quot;&gt;?&lt;/td&gt;', false);
        $response->assertDontSee('class=&quot;value&quot;&gt;13&lt;/td&gt;', false);
        $response->assertDontSee('Shipment');
    }

    /**
     * At espionage 8, defender sees exact per-type counts (still no shipment).
     */
    public function testEspionageLevelEightShowsFullComposition(): void
    {
        $response = $this->fetchDefenderEventListWithIncomingAttack(8, 13);

        $response->assertStatus(200);
        $response->assertSee('Attack');
        $response->assertSee('Light Fighter');
        $response->assertSee('class=&quot;value&quot;&gt;13&lt;/td&gt;', false);
        $response->assertDontSee('Shipment');
    }

    /**
     * Own outgoing fleets always show full composition even with espionage 0.
     */
    public function testOwnFleetAlwaysShowsFullDetailsAtEspionageZero(): void
    {
        $this->basicSetup();
        $this->playerSetResearchLevel('espionage_technology', 0);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 3);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);
        $response->assertSee('Light Fighter');
        $response->assertSee('class=&quot;value&quot;&gt;3&lt;/td&gt;', false);
        $response->assertSee('Shipment');
    }

    /**
     * Send an attack from the current user to a foreign planet, then fetch the
     * event list as the defender with the given espionage level.
     */
    private function fetchDefenderEventListWithIncomingAttack(int $defenderEspionageLevel, int $shipCount)
    {
        $this->basicSetup();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), $shipCount);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        $defenderPlayer = $foreignPlanet->getPlayer();
        $defenderPlayer->setResearchLevel('espionage_technology', $defenderEspionageLevel);

        // Switch auth context to the defender so event list is built for them.
        $this->currentUserId = $defenderPlayer->getId();
        $this->be(User::findOrFail($this->currentUserId));
        $this->reloadApplication();

        return $this->get('/ajax/fleet/eventlist/fetch');
    }
}

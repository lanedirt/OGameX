<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use Tests\AccountTestCase;

/**
 * Test that the galaxy page works as expected.
 */
class GalaxyTest extends AccountTestCase
{
    /**
     * Verify that debris fields are shown on the galaxy page.
     */
    public function testGalaxyDebrisFieldShown(): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();

        // Add debris field to the current planet.
        $debrisField = resolve(DebrisFieldService::class);
        $debrisField->loadOrCreateForCoordinates($coordinates);
        $debrisField->appendResources(new Resources(5555, 6666, 7777, 0));
        $debrisField->save();

        // Request overview page first to make sure the csrf_token is set.
        $response = $this->get('/');

        // Request the galaxy page AJAX call for current coordinates.
        // POST to http://localhost/ajax/galaxy with data:
        // galaxy = current galaxy
        // system = current system
        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
        ]);

        $responseContent = $response->getContent();
        if ($response->status() !== 200 || ($responseContent !== false && stristr($responseContent, 'debris_1') === false)) {
            // Include HTML error message (first 2k chars)
            $customMessage = 'Galaxy page does not contain debris field.';
            if (!empty($responseContent)) {
                $customMessage .= "\nGalaxy ajax response: " . substr($responseContent, 0, 2000);
            }
            $this->fail($customMessage);
        }

        // Check that the response contains the debris field by checking for the name and resource amounts.
        $response->assertSeeText('debris_1');
        $response->assertSeeText('5555');
        $response->assertSeeText('6666');
        $response->assertSeeText('7777');
    }

    /**
     * Verify galaxy AJAX returns real fleet slot counts from the player.
     */
    public function testGalaxyAjaxReturnsRealFleetSlots(): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();
        $player = $this->planetService->getPlayer();

        $this->get('/');

        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
        ]);

        $response->assertOk();
        $response->assertJsonPath('system.usedFleetSlots', $player->getFleetSlotsInUse());
        $response->assertJsonPath('system.maximumFleetSlots', $player->getFleetSlotsMax());
        $response->assertJsonPath('system.canFly', true);
        $response->assertJsonPath('system.hasAdmiral', $player->hasAdmiral());
    }

    /**
     * Verify canFly is false when all fleet slots are in use.
     */
    public function testGalaxyAjaxCanFlyFalseWhenSlotsFull(): void
    {
        $this->playerSetResearchLevel('computer_technology', 0);

        $coordinates = $this->planetService->getPlanetCoordinates();
        $player = $this->planetService->getPlayer();

        // Fill the only available fleet slot with an active mission.
        $mission = new FleetMission();
        $mission->user_id = $player->getId();
        $mission->planet_id_from = $this->planetService->getPlanetId();
        $mission->galaxy_from = $coordinates->galaxy;
        $mission->system_from = $coordinates->system;
        $mission->position_from = $coordinates->position;
        $mission->type_from = PlanetType::Planet->value;
        $mission->galaxy_to = $coordinates->galaxy;
        $mission->system_to = $coordinates->system;
        $mission->position_to = $coordinates->position;
        $mission->type_to = PlanetType::Planet->value;
        $mission->mission_type = 3;
        $mission->time_departure = (int) Date::now()->timestamp;
        $mission->time_arrival = (int) Date::now()->addHour()->timestamp;
        $mission->processed = 0;
        $mission->processed_hold = 0;
        $mission->canceled = 0;
        $mission->small_cargo = 1;
        $mission->save();

        $this->assertEquals(1, $player->getFleetSlotsInUse());
        $this->assertEquals(1, $player->getFleetSlotsMax());

        $this->get('/');

        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
        ]);

        $response->assertOk();
        $response->assertJsonPath('system.usedFleetSlots', 1);
        $response->assertJsonPath('system.maximumFleetSlots', 1);
        $response->assertJsonPath('system.canFly', false);
    }

    /**
     * Verify empty-slot colonisation tooltip warns about missing astrophysics and disables the link.
     */
    public function testGalaxyColoniseTooltipShowsAstrophysicsWarning(): void
    {
        $this->playerSetResearchLevel('astrophysics', 0);
        $this->planetAddUnit('colony_ship', 1);

        $coordinates = $this->planetService->getPlanetCoordinates();

        $this->get('/');

        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
        ]);

        $response->assertOk();
        $response->assertJsonPath('system.canColonize', false);

        $galaxyContent = $response->json('system.galaxyContent');
        $this->assertIsArray($galaxyContent);

        $emptySlot = null;
        foreach ($galaxyContent as $row) {
            if (($row['positionFilters'] ?? null) === 'empty_filter') {
                $emptySlot = $row;
                break;
            }
        }

        $this->assertNotNull($emptySlot, 'Expected an empty galaxy slot for colonisation tooltip assertion.');

        $coloniseMission = null;
        foreach ($emptySlot['availableMissions'] as $mission) {
            if (($mission['missionType'] ?? null) === 7) {
                $coloniseMission = $mission;
                break;
            }
        }

        $this->assertNotNull($coloniseMission, 'Expected a colonisation mission on the empty slot.');
        $this->assertSame('#', $coloniseMission['link']);
        $this->assertStringContainsString(__('t_ingame.galaxy.astro_required'), $coloniseMission['description']);
    }
}

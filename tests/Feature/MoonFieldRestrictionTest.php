<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\Planet;
use OGame\Models\Resources as GameResources;
use OGame\Services\ObjectService;
use Tests\MoonTestCase;

/**
 * Test that planet field restrictions work correctly for moons.
 */
class MoonFieldRestrictionTest extends MoonTestCase
{
    use DatabaseTransactions;

    /**
     * Test that a building cannot be built when moon fields are full.
     *
     * @return void
     */
    public function testCannotBuildWhenMoonFieldsAreFull(): void
    {
        // Set the moon to have very few fields and fill them up
        $moonModel = Planet::where('id', $this->moonService->getPlanetId())->first();
        $moonModel->field_max = 3;
        $moonModel->save();

        // Fill up all 3 fields
        $moonModel->robot_factory = 1;
        $moonModel->shipyard = 1;
        $moonModel->lunar_base = 1;
        $moonModel->save();

        // Reload the moon service to get updated data
        $this->moonService->reloadPlanet();

        // Verify that building count equals max fields
        // Note: lunar_base level 1 adds 3 bonus fields, so total = 3 + 3 = 6
        $this->assertEquals(3, $this->moonService->getBuildingCount());
        $this->assertEquals(6, $this->moonService->getPlanetFieldMax());

        // Now use up the bonus fields by setting more buildings
        $moonModel->research_lab = 1;
        $moonModel->nano_factory = 1;
        $moonModel->sensor_phalanx = 1;
        $moonModel->save();
        $this->moonService->reloadPlanet();

        // Building count should now be 6 (equal to max fields including lunar_base bonus)
        $this->assertEquals(6, $this->moonService->getBuildingCount());
        $this->assertEquals(6, $this->moonService->getPlanetFieldMax());

        // Set hyperspace_technology to level 7 (required for jump_gate)
        $this->moonService->getPlayer()->setResearchLevel('hyperspace_technology', 7);

        // Add resources to build jump_gate
        $this->moonAddResources(new GameResources(3000000, 5000000, 3000000));

        // Try to build jump_gate - should fail due to field limit
        $object = ObjectService::getObjectByMachineName('jump_gate');

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive an error response, not success
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
    }

    /**
     * Test that lunar_base increases max fields allowing more buildings on moons.
     *
     * @return void
     */
    public function testLunarBaseIncreasesMaxFields(): void
    {
        // Set the moon to have 3 base fields
        $moonModel = Planet::where('id', $this->moonService->getPlanetId())->first();
        $moonModel->field_max = 3;
        $moonModel->lunar_base = 0;
        $moonModel->save();

        // Build 3 facilities
        $moonModel->robot_factory = 1;
        $moonModel->shipyard = 1;
        $moonModel->research_lab = 1;
        $moonModel->save();
        $this->moonService->reloadPlanet();

        // Verify max fields is 3 (no lunar_base yet)
        $this->assertEquals(3, $this->moonService->getPlanetFieldMax());
        $this->assertEquals(3, $this->moonService->getBuildingCount());

        // Now upgrade lunar_base to level 1 (should add 3 fields)
        $moonModel->lunar_base = 1;
        $moonModel->save();
        $this->moonService->reloadPlanet();

        // Verify max fields is now 6 (3 base + 3 from lunar_base level 1)
        $this->assertEquals(6, $this->moonService->getPlanetFieldMax());

        // Building count should be 4 (robot_factory + shipyard + research_lab + lunar_base)
        $this->assertEquals(4, $this->moonService->getBuildingCount());

        // Verify max fields is greater than building count (6 > 4)
        $maxFields = $this->moonService->getPlanetFieldMax();
        $buildingCount = $this->moonService->getBuildingCount();
        $this->assertGreaterThan($buildingCount, $maxFields);
    }

    /**
     * Add resources to the moon.
     *
     * @param GameResources $resources
     * @return void
     */
    private function moonAddResources(GameResources $resources): void
    {
        $moonModel = Planet::where('id', $this->moonService->getPlanetId())->first();
        $moonModel->metal += $resources->metal->get();
        $moonModel->crystal += $resources->crystal->get();
        $moonModel->deuterium += $resources->deuterium->get();
        $moonModel->metal_production = 1000;
        $moonModel->crystal_production = 1000;
        $moonModel->deuterium_production = 1000;
        $moonModel->save();
        $this->moonService->reloadPlanet();
    }
}

<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\DB;
use Tests\FleetDispatchTestCase;

/**
 * Test that the fleet dispatch page embeds floored planet resources into JS.
 * This covers the #1131 send-all bug: getRounded() rounds up, so JS is told
 * more metal/crystal/deuterium than exists and max-cargo send fails.
 */
class FleetDispatchPageResourcesTest extends FleetDispatchTestCase
{
    protected int $missionType = 3;
    protected string $missionName = 'Transport';

    /**
     * No extra buildings or ships are required to render the fleet page.
     */
    protected function basicSetup(): void
    {
    }

    /**
     * Fractional planet resources must be floored into fleet JS, not rounded up.
     *
     * getRounded() would turn 4999.58 / 4999.59 / 9990.7 into 5000 / 5000 / 9991.
     */
    public function testFleetPageEmbedsFlooredPlanetResources(): void
    {
        DB::table('planets')->where('id', $this->planetService->getPlanetId())->update([
            'metal' => 4999.58,
            'crystal' => 4999.59,
            'deuterium' => 9990.7,
        ]);
        $this->planetService->reloadPlanet();

        $response = $this->get('/fleet');
        $response->assertStatus(200);

        $response->assertSee('var metalOnPlanet = 4999;', false);
        $response->assertSee('var crystalOnPlanet = 4999;', false);
        $response->assertSee('var deuteriumOnPlanet = 9990;', false);

        $response->assertDontSee('var metalOnPlanet = 5000;', false);
        $response->assertDontSee('var crystalOnPlanet = 5000;', false);
        $response->assertDontSee('var deuteriumOnPlanet = 9991;', false);
    }
}

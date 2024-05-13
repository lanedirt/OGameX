<?php

namespace Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Models\Resources;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchGenericTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 3;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Transport';

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('colony_ship', 1);
    }

    /**
     * Test that deducting resources without saving the planet does not still save it
     * in another request. This is to make sure the test logic works correctly and
     * objects are not cached between requests in the same test.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDeductResourcesWithoutSavingPlanetIgnored(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Do initial HTTP request.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload application to make sure the planet is not cached.
        $this->reloadApplication();

        // Deduct resources from the planet but don't save the planet itself.
        // This should NOT deduct the resources.
        $this->planetService->deductResources(new Resources(5000, 5000, 0, 0), false);

        // Reload application to make sure the planet is not cached.
        // NOTE: without this method, the planet would be cached and the resources would be deducted
        // during the next HTTP request in this test.
        $this->reloadApplication();

        // Do another HTTP request.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the resources are NOT deducted.
        $this->planetService->reloadPlanet();
        $this->assertTrue($this->planetService->hasResources(new Resources(5000, 5000, 0, 0)), 'Resources are deducted from planet without saving it. State seems to be cached between requests. Check the AccountTestCase::reloadApplication() test logic.');
    }

}

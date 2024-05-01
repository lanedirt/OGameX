<?php

namespace Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchColoniseTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 7;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Colonise';

    protected bool $hasReturnMission = false;

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function basicSetup(): void
    {
        // Set the robotics factory to level 2
        $this->planetSetObjectLevel('robot_factory', 2);
        // Set shipyard to level 1.
        $this->planetSetObjectLevel('shipyard', 1);
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);
        // Set energy technology to level 1.
        $this->playerSetResearchLevel('energy_technology', 1);
        // Set combustion drive to level 1.
        $this->playerSetResearchLevel('combustion_drive', 1);
        // Add light cargo ship to the planet.
        $this->planetAddUnit('small_cargo', 5);
        // Add colony ship to the planet.
        $this->planetAddUnit('colony_ship', 1);
    }

    /**
     * @throws BindingResolutionException
     */
    public function testDispatchFleetCheckTargetResponse(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that check request to dispatch fleet to empty position succeeds with colony ship.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);

        // TODO: finish test so it makes a request to "/ajax/fleet/dispatch/check-target" and asserts the response.
        // TODO: also add this check to the other fleet dispatch tests.
    }

    /**
     * @throws BindingResolutionException
     */
    public function testDispatchFleetToNotEmptyPositionFails(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a planet position that is already colonized.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        // Expecting 500 error.
        $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0), 500);
    }
}

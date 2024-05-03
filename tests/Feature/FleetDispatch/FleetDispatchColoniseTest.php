<?php

namespace Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
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
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('colony_ship', 1);
    }

    /**
     * Assert that check request to dispatch fleet to empty position succeeds with colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testFleetCheckWithColonyShipSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails without colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testFleetCheckWithoutColonyShipError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Send fleet to a planet position that is already colonized.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetToNotEmptyPositionFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        // Expecting 500 error.
        $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0), 500);
    }

    /**
     * Send fleet to empty planet without colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetWithoutColonyShipFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        // Expecting 500 error.
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(0, 0, 0, 0), 500);
    }
}

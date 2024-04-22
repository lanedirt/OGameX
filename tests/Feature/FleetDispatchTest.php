<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use Tests\AccountTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchTest extends AccountTestCase
{
    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function basicSetup(): void
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
    }

    private function sendTransportMissionToSecondPlanet(UnitCollection $units, Resources $resources, int $assertStatus = 200) : void {
        // Convert units to array.
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        // Send fleet to the second planet of the test user.
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $this->secondPlanetService->getPlanetCoordinates()->galaxy,
            'system' => $this->secondPlanetService->getPlanetCoordinates()->system,
            'position' => $this->secondPlanetService->getPlanetCoordinates()->position,
            'type' => 1,
            'mission' => 3, // Transport mission
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        // Assert that the fleet was dispatched successfully.
        $post->assertStatus($assertStatus);
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of units from planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductUnits(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 4, 'Small Cargo ship not deducted from planet after fleet dispatch.');
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of resources from planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductResources(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Get beginning resources of the planet.
        $beginningMetal = $this->planetService->metal()->get();
        $beginningCrystal = $this->planetService->crystal()->get();

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the resources were deducted from the planet.
        $this->assertResourcesOnPage($response, new Resources($beginningMetal - 100, $beginningCrystal - 100, 0, 0));
    }

    /**
     * Verify that dispatching a fleet with more resources than is on planet fails.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductTooMuchResources(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(4500, 100, 0, 0), 500);
    }

    /**
     * Verify that dispatching a fleet with more units than is on planet fails.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductTooMuchUnits(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 10);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0), 500);
    }
}

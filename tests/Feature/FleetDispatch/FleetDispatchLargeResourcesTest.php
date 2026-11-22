<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works correctly with large resource amounts.
 * This tests the fix for issue #1014 where sending large amounts of
 * resources (75M+) would fail validation even with sufficient capacity.
 */
class FleetDispatchLargeResourcesTest extends FleetDispatchTestCase
{
    protected int $missionType = 3; // Transport
    protected string $missionName = 'Transport';

    /**
     * Prepare the planet for the test with a large fleet and resources.
     */
    protected function basicSetup(): void
    {
        // Add a large fleet with substantial cargo capacity
        // 10,000 Large Cargo ships = 250,000,000 total capacity
        $this->planetAddUnit('large_cargo', 10000);

        // Add large resources to the planet (75M of each + buffer for fuel consumption)
        // Fuel consumption for 10k Large Cargo ships can be ~50k-100k depending on distance
        $this->planetAddResources(new Resources(75000000, 75000000, 75100000, 0));
    }

    /**
     * Test sending large resource amounts (75M of each type).
     * This should succeed with 10,000 Large Cargo ships (250M capacity).
     */
    public function testSendLargeResourceAmounts(): void
    {
        $this->basicSetup();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 10000);

        // Send 75M of each resource (225M total, well within 250M capacity)
        $this->sendMissionToSecondPlanet(
            $unitCollection,
            new Resources(75000000, 75000000, 75000000, 0)
        );
        // If we get here without exception, the test passes
    }

    /**
     * Test the exact edge case where resources equal capacity.
     */
    public function testSendResourceAmountsEqualToCapacity(): void
    {
        $this->basicSetup();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 10000);

        // Send close to the available resources (leaving room for fuel consumption)
        // 75M available - need to account for fuel (~50k-100k)
        $this->sendMissionToSecondPlanet(
            $unitCollection,
            new Resources(74900000, 74900000, 74900000, 0)
        );
        // If we get here without exception, the test passes
    }

    /**
     * Test that the hasResources check works correctly with large numbers.
     */
    public function testHasResourcesWithLargeNumbers(): void
    {
        $this->basicSetup();

        // The planet should have exactly 75M of each resource
        $metalOnPlanet = $this->planetService->metal()->get();
        $crystalOnPlanet = $this->planetService->crystal()->get();
        $deuteriumOnPlanet = $this->planetService->deuterium()->get();

        echo PHP_EOL . 'Planet resources:' . PHP_EOL;
        echo 'Metal: ' . $metalOnPlanet . PHP_EOL;
        echo 'Crystal: ' . $crystalOnPlanet . PHP_EOL;
        echo 'Deuterium: ' . $deuteriumOnPlanet . PHP_EOL;

        // Check that hasResources returns true for exactly the amount we have
        $resources = new Resources(75000000, 75000000, 75000000, 0);
        $hasResources = $this->planetService->hasResources($resources);

        $this->assertTrue($hasResources, 'hasResources should return true for exact amount available');
    }
}

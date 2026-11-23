<?php

namespace Tests\Unit;

use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\User;
use OGame\Services\MilitaryStatisticsService;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test class for MilitaryStatisticsService.
 * Tests calculation of military points from various unit types.
 */
class MilitaryStatisticsServiceTest extends UnitTestCase
{
    private MilitaryStatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MilitaryStatisticsService();
    }

    /**
     * Test calculating military points from military ships (100%).
     */
    public function testCalculateMilitaryPointsFromMilitaryShips(): void
    {
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($lightFighter, 10);

        $points = $this->service->calculateMilitaryPoints($unitCollection);

        // Light fighter costs 3000 metal + 1000 crystal = 4000 total
        // 10 light fighters = 40,000 resources
        // Military ships count 100%, so 40,000 / 1000 = 40 points
        $this->assertEquals(40, $points);
    }

    /**
     * Test calculating military points from civil ships (50%).
     */
    public function testCalculateMilitaryPointsFromCivilShips(): void
    {
        $smallCargo = ObjectService::getShipObjectByMachineName('small_cargo');
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($smallCargo, 10);

        $points = $this->service->calculateMilitaryPoints($unitCollection);

        // Small cargo costs 2000 metal + 2000 crystal = 4000 total
        // 10 small cargo = 40,000 resources
        // Civil ships count 50%, so (40,000 * 0.5) / 1000 = 20 points
        $this->assertEquals(20, $points);
    }

    /**
     * Test calculating military points from defense units (100%).
     */
    public function testCalculateMilitaryPointsFromDefense(): void
    {
        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');
        $this->assertEquals(GameObjectType::Defense, $rocketLauncher->type);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($rocketLauncher, 10);

        $points = $this->service->calculateMilitaryPoints($unitCollection);

        // Rocket launcher costs 2000 metal = 2000 total
        // 10 rocket launchers = 20,000 resources
        // Defense counts 100%, so 20,000 / 1000 = 20 points
        $this->assertEquals(20, $points);
    }

    /**
     * Test calculating military points from mixed unit types.
     */
    public function testCalculateMilitaryPointsFromMixedUnits(): void
    {
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $smallCargo = ObjectService::getShipObjectByMachineName('small_cargo');
        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($lightFighter, 10);   // 40,000 * 100% = 40,000
        $unitCollection->addUnit($smallCargo, 10);      // 40,000 * 50% = 20,000
        $unitCollection->addUnit($rocketLauncher, 10);  // 20,000 * 100% = 20,000
        // Total: 80,000 / 1000 = 80 points

        $points = $this->service->calculateMilitaryPoints($unitCollection);
        $this->assertEquals(80, $points);
    }

    /**
     * Test calculating military points from espionage probes (civil ship, 50%).
     */
    public function testCalculateMilitaryPointsFromEspionageProbes(): void
    {
        $espionageProbe = ObjectService::getShipObjectByMachineName('espionage_probe');
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($espionageProbe, 1000);

        $points = $this->service->calculateMilitaryPoints($unitCollection);

        // Espionage probe is a civil ship (50% value)
        // Cost is minimal but should still count at 50%
        $this->assertGreaterThan(0, $points);
    }

    /**
     * Test calculating military points from machine name and amount.
     */
    public function testCalculateMilitaryPointsFromMachineName(): void
    {
        $points = $this->service->calculateMilitaryPointsFromMachineName('light_fighter', 10);

        // Light fighter: 4000 * 10 * 100% / 1000 = 40 points
        $this->assertEquals(40, $points);
    }

    /**
     * Test calculating military points from invalid machine name returns zero.
     */
    public function testCalculateMilitaryPointsFromInvalidMachineName(): void
    {
        $points = $this->service->calculateMilitaryPointsFromMachineName('invalid_unit', 10);
        $this->assertEquals(0, $points);
    }

    /**
     * Test calculating military points with zero amount returns zero.
     */
    public function testCalculateMilitaryPointsWithZeroAmount(): void
    {
        $points = $this->service->calculateMilitaryPointsFromMachineName('light_fighter', 0);
        $this->assertEquals(0, $points);
    }

    /**
     * Test adding lost points to user - integration test.
     * Note: This test only verifies the increment logic without saving to DB.
     */
    public function testAddLostPoints(): void
    {
        // Use a partial mock to allow property access but mock save()
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['save'])
            ->getMock();

        $user->military_units_lost_points = 100;

        // Expect save to be called once
        $user->expects($this->once())->method('save');

        $this->service->addLostPoints($user, 50);

        // Verify the value was incremented
        $this->assertEquals(150, $user->military_units_lost_points);
    }

    /**
     * Test adding destroyed points to user - integration test.
     * Note: This test only verifies the increment logic without saving to DB.
     */
    public function testAddDestroyedPoints(): void
    {
        // Use a partial mock to allow property access but mock save()
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['save'])
            ->getMock();

        $user->military_units_destroyed_points = 100;

        // Expect save to be called once
        $user->expects($this->once())->method('save');

        $this->service->addDestroyedPoints($user, 50);

        // Verify the value was incremented
        $this->assertEquals(150, $user->military_units_destroyed_points);
    }

    /**
     * Test adding zero points doesn't save unnecessarily.
     */
    public function testAddZeroPointsDoesNothing(): void
    {
        $user = $this->createMock(User::class);
        $user->military_units_lost_points = 100;

        // Mock should not call save() when points are 0
        $user->expects($this->never())->method('save');

        $this->service->addLostPoints($user, 0);
    }
}

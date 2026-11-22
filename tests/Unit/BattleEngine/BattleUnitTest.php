<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\Models\BattleUnit;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test class for BattleUnit model.
 */
class BattleUnitTest extends UnitTestCase
{
    /**
     * Test that BattleUnit correctly stores fleet mission ID and owner ID.
     */
    public function testBattleUnitStoresFleetMissionIdAndOwnerId(): void
    {
        $unitObject = ObjectService::getUnitObjectByMachineName('light_fighter');

        $fleetMissionId = 123;
        $ownerId = 456;

        $battleUnit = new BattleUnit(
            $unitObject,
            4000,  // structural integrity
            10,    // shield points
            50,    // attack power
            $fleetMissionId,
            $ownerId
        );

        $this->assertEquals($fleetMissionId, $battleUnit->fleetMissionId, 'Fleet mission ID should be correctly stored');
        $this->assertEquals($ownerId, $battleUnit->ownerId, 'Owner ID should be correctly stored');
    }

    /**
     * Test that BattleUnit correctly sets other properties.
     */
    public function testBattleUnitSetsAllProperties(): void
    {
        $unitObject = ObjectService::getUnitObjectByMachineName('cruiser');

        $structuralIntegrity = 27000;
        $shieldPoints = 50;
        $attackPower = 400;
        $fleetMissionId = 789;
        $ownerId = 101;

        $battleUnit = new BattleUnit(
            $unitObject,
            $structuralIntegrity,
            $shieldPoints,
            $attackPower,
            $fleetMissionId,
            $ownerId
        );

        // Verify unit object
        $this->assertEquals($unitObject, $battleUnit->unitObject);

        // Verify hull plating (structural integrity / 10)
        $expectedHullPlating = $structuralIntegrity / 10;
        $this->assertEquals($expectedHullPlating, $battleUnit->originalHullPlating);
        $this->assertEquals($expectedHullPlating, $battleUnit->currentHullPlating);

        // Verify shield points
        $this->assertEquals($shieldPoints, $battleUnit->originalShieldPoints);
        $this->assertEquals($shieldPoints, $battleUnit->currentShieldPoints);

        // Verify attack power
        $this->assertEquals($attackPower, $battleUnit->attackPower);

        // Verify ownership tracking
        $this->assertEquals($fleetMissionId, $battleUnit->fleetMissionId);
        $this->assertEquals($ownerId, $battleUnit->ownerId);
    }

    /**
     * Test that defender planet units use fleetMissionId = 0.
     */
    public function testDefenderPlanetUnitsUseZeroFleetMissionId(): void
    {
        $unitObject = ObjectService::getUnitObjectByMachineName('rocket_launcher');

        // Defender planet units (not from a fleet mission) should use fleetMissionId = 0
        $battleUnit = new BattleUnit(
            $unitObject,
            2000,   // structural integrity
            20,     // shield points
            80,     // attack power
            0,      // fleetMissionId = 0 for stationary defenses
            999     // planet owner ID
        );

        $this->assertEquals(0, $battleUnit->fleetMissionId, 'Stationary planet units should have fleetMissionId = 0');
        $this->assertEquals(999, $battleUnit->ownerId, 'Planet units should have planet owner as ownerId');
    }
}

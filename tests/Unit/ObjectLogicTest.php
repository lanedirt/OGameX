<?php

namespace Tests\Unit;

use OGame\Services\Objects\ObjectService;
use PHPUnit\Framework\TestCase;

class ObjectLogicTest extends TestCase
{
    /**
     * Verify that object service returns actual objects.
     */
    public function testObjectServiceContents(): void
    {
        $objectService = new ObjectService();

        $this->assertTrue(count($objectService->getObjects()) > 1);
        $this->assertTrue(count($objectService->getBuildingObjects()) > 1);
        $this->assertTrue(count($objectService->getStationObjects()) > 1);
        $this->assertTrue(count($objectService->getResearchObjects()) > 1);
        $this->assertTrue(count($objectService->getUnitObjects()) > 1);
        $this->assertTrue(count($objectService->getShipObjects()) > 1);
        $this->assertTrue(count($objectService->getMilitaryShipObjects()) > 1);
        $this->assertTrue(count($objectService->getCivilShipObjects()) > 1);
        $this->assertTrue(count($objectService->getDefenseObjects()) > 1);
    }

    /**
     * Test that all ship objects have properties such as structural integrity, shield etc. defined.
     */
    public function testShipProperties() {
        $objectService = new ObjectService();

        $ships = $objectService->getShipObjects();
        foreach ($ships as $ship) {
            $this->assertNotNull($ship->properties->structural_integrity);
            $this->assertNotNull($ship->properties->shield);
            $this->assertNotNull($ship->properties->attack);
            $this->assertNotNull($ship->properties->speed);
            $this->assertNotNull($ship->properties->capacity);
            $this->assertNotNull($ship->properties->fuel);
        }
    }

    /**
     * Test that all defense objects have properties such as structural integrity, shield etc. defined.
     */
    public function testDefenceProperties() {
        $objectService = new ObjectService();

        $objects = $objectService->getDefenseObjects();
        foreach ($objects as $object) {
            $this->assertNotNull($object->properties->structural_integrity);
            $this->assertNotNull($object->properties->shield);
            $this->assertNotNull($object->properties->attack);
        }
    }
}

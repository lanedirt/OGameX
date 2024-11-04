<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class ObjectLogicTest extends UnitTestCase
{
    /**
     * Verify that object service returns actual objects.
     */
    public function testObjectServiceContents(): void
    {
        $this->assertTrue(count(ObjectService::getObjects()) > 1);
        $this->assertTrue(count(ObjectService::getBuildingObjects()) > 1);
        $this->assertTrue(count(ObjectService::getStationObjects()) > 1);
        $this->assertTrue(count(ObjectService::getResearchObjects()) > 1);
        $this->assertTrue(count(ObjectService::getUnitObjects()) > 1);
        $this->assertTrue(count(ObjectService::getShipObjects()) > 1);
        $this->assertTrue(count(ObjectService::getMilitaryShipObjects()) > 1);
        $this->assertTrue(count(ObjectService::getCivilShipObjects()) > 1);
        $this->assertTrue(count(ObjectService::getDefenseObjects()) > 1);
    }

    /**
     * Test that all ship objects have properties such as structural integrity, shield etc. defined.
     */
    public function testShipProperties(): void
    {
        $ships = ObjectService::getShipObjects();
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
    public function testDefenseProperties(): void
    {
        $objects = ObjectService::getDefenseObjects();
        foreach ($objects as $object) {
            $this->assertNotNull($object->properties->structural_integrity);
            $this->assertNotNull($object->properties->shield);
            $this->assertNotNull($object->properties->attack);
        }
    }
}

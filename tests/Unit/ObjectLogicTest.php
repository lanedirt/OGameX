<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
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
        $this->assertTrue(count($objectService->getDefenceObjects()) > 1);
    }
}

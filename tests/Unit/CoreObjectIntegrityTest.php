<?php

namespace Tests\Unit;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Services\ObjectService;
use Tests\TestCase;

/**
 * Guards the core object catalog. A module-system regression once caused core
 * ships to be returned twice by the per-category accessors; these assertions
 * pin the core catalog to a single instance per object.
 */
class CoreObjectIntegrityTest extends TestCase
{
    public function test_core_ship_objects_are_not_duplicated(): void
    {
        $this->assertUnique(ObjectService::getShipObjects());
        $this->assertUnique(ObjectService::getMilitaryShipObjects());
        $this->assertUnique(ObjectService::getCivilShipObjects());
    }

    public function test_core_building_station_research_and_defense_objects_are_not_duplicated(): void
    {
        $this->assertUnique(ObjectService::getBuildingObjects());
        $this->assertUnique(ObjectService::getStationObjects());
        $this->assertUnique(ObjectService::getResearchObjects());
        $this->assertUnique(ObjectService::getDefenseObjects());
    }

    /**
     * @param  array<int, GameObject>  $objects
     */
    private function assertUnique(array $objects): void
    {
        $ids = array_map(static fn (GameObject $object): int => $object->id, $objects);
        $names = array_map(static fn (GameObject $object): string => $object->machine_name, $objects);

        $this->assertCount(count($objects), array_unique($ids), 'Duplicate object IDs detected.');
        $this->assertCount(count($objects), array_unique($names), 'Duplicate object machine names detected.');
    }
}

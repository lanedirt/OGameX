<?php

namespace Tests\Feature;

use OGame\Models\ProductionIndex;
use OGame\Services\ObjectService;
use Tests\IsolatedAccountTestCase;

/**
 * Test that the energy values shown on the resource settings page match the
 * values shown in the resources header bar.
 */
class ResourceSettingsEnergyTest extends IsolatedAccountTestCase
{
    /**
     * Put the planet in an energy deficit so the production factor is below 100%.
     * The production factor must never be applied to the energy columns.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->planetSetObjectLevel('metal_mine', 20);
        $this->planetSetObjectLevel('crystal_mine', 20);
        $this->planetSetObjectLevel('deuterium_synthesizer', 20);
        $this->planetSetObjectLevel('solar_plant', 15);
        $this->planetService->updateResourceProductionStats();

        $this->assertLessThan(100, $this->planetService->getResourceProductionFactor(), 'Test setup should result in an energy deficit.');
    }

    /**
     * The totals row must show the same per hour values as the header.
     */
    public function testSettingsTotalsMatchHeader(): void
    {
        $response = $this->get('/resources/settings');
        $response->assertStatus(200);
        $this->planetService->reloadPlanet();

        $production_total = $response->viewData('production_total');
        $this->assertInstanceOf(ProductionIndex::class, $production_total);

        $this->assertEquals($this->planetService->getMetalProductionPerHour(), $production_total->total->metal->get());
        $this->assertEquals($this->planetService->getCrystalProductionPerHour(), $production_total->total->crystal->get());
        $this->assertEquals($this->planetService->getDeuteriumProductionPerHour(), $production_total->total->deuterium->get());
        $this->assertEquals($this->planetService->energy()->get(), $production_total->total->energy->get());
    }

    /**
     * Energy producing buildings must show their nominal (100%) energy production,
     * which is what the header uses for the energy production value.
     */
    public function testEnergyBuildingRowsShowNominalProduction(): void
    {
        $response = $this->get('/resources/settings');
        $response->assertStatus(200);
        $this->planetService->reloadPlanet();

        $building_energy_rows = $response->viewData('building_energy_rows');
        $this->assertIsArray($building_energy_rows);

        $solar_plant = ObjectService::getObjectByMachineName('solar_plant');
        $solar_plant_energy = 0;
        foreach ($building_energy_rows as $row) {
            if ($row['id'] === $solar_plant->id) {
                $solar_plant_energy = $row['production']->energy->get();
            }
        }

        // The solar plant is the only energy producer on this planet.
        $this->assertEquals($this->planetService->energyProduction()->get(), $solar_plant_energy);
    }

    /**
     * Energy consuming buildings must show their nominal (100%) energy consumption,
     * and the actual consumption must be the nominal consumption reduced once by
     * the production factor.
     */
    public function testResourceBuildingRowsShowNominalConsumption(): void
    {
        $response = $this->get('/resources/settings');
        $response->assertStatus(200);
        $this->planetService->reloadPlanet();

        $building_resource_rows = $response->viewData('building_resource_rows');
        $this->assertIsArray($building_resource_rows);
        $this->assertNotEmpty($building_resource_rows);

        $production_factor = $this->planetService->getResourceProductionFactor();
        $nominal_consumption_total = 0;
        foreach ($building_resource_rows as $row) {
            $nominal_consumption = $row['production']->energy->get();
            $nominal_consumption_total += abs($nominal_consumption);

            $this->assertEquals(floor($nominal_consumption * ($production_factor / 100)), $row['actual_energy_use']);
        }

        // Each row is floored individually, so allow a rounding difference of at most one per row.
        $this->assertEqualsWithDelta($this->planetService->energyConsumption()->get(), $nominal_consumption_total, count($building_resource_rows));
    }
}

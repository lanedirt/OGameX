<?php

namespace Tests\Feature;

use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test that level-0 mines show required energy in the building overlay (#1320).
 */
class MineEnergyAtLevelZeroTest extends AccountTestCase
{
    /**
     * Rendering the resources overlay for a level-0 metal mine must report energy needed.
     * This exercises ObjectAjaxTrait (not just getObjectProduction).
     */
    public function testLevelZeroMineOverlayShowsEnergyRequirement(): void
    {
        $this->planetSetObjectLevel('metal_mine', 0);
        $this->planetSetObjectLevel('solar_plant', 1);

        $metalMine = ObjectService::getObjectByMachineName('metal_mine');
        $response = $this->get('ajax/resources?technology=' . $metalMine->id);

        $response->assertStatus(200);

        $overlayHtml = $response->json('content.technologydetails');
        $this->assertNotEmpty($overlayHtml, 'Building overlay HTML should be present in AJAX response');

        $this->assertStringContainsString(
            'additional_energy_consumption',
            $overlayHtml,
            'Level-0 metal mine overlay should show energy needed for level 1'
        );

        $this->assertMatchesRegularExpression(
            '/class="additional_energy_consumption".*?data-value="([1-9]\d*)"/s',
            $overlayHtml,
            'Level-0 metal mine overlay should report a non-zero energy requirement'
        );
    }
}

<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

/**
 * Test that Facilities page header functionality works as expected.
 */
class FacilitiesHeaderTest extends AccountTestCase
{
    /**
     * Test that the facilities page loads successfully and contains a header element.
     *
     * @return void
     */
    public function testFacilitiesPageHasHeaderElement(): void
    {
        $response = $this->get('/facilities');

        $response->assertStatus(200);
        $response->assertSee('Facilities - ');
    }

    /**
     * Test that the facilities page contains a header with background-image.
     * This catches issues where the header_filename is not generated correctly.
     *
     * @return void
     */
    public function testFacilitiesPageHeaderHasBackgroundImage(): void
    {
        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();
        if ($content === false) {
            $content = '';
        }

        // Verify the header element exists with a background-image style
        $this->assertMatchesRegularExpression(
            '/<header[^>]*style=["\'][^"\']*background-image:\s*url\([^)]+\)/',
            $content,
            'The facilities page should have a header element with a valid background-image CSS url() function'
        );
    }

    /**
     * Test that the header filename follows the expected pattern.
     * The header filename should match the pattern: {biome}_{building_ids}.jpg
     *
     * @return void
     */
    public function testFacilitiesPageHeaderFilenamePattern(): void
    {
        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();
        if ($content === false) {
            $content = '';
        }

        // Verify the header image path follows the expected pattern
        // Pattern: /img/headers/facilities/{biome}_{optional_building_ids}.jpg
        // Where biome is: dry, desert, normal, ice, etc.
        // And building_ids (if present) are: _14, _15, _21, _31, _33, _34 in ascending order
        $this->assertMatchesRegularExpression(
            '#/img/headers/facilities/(dry|desert|normal|ice|water|gas)(?:_(?:14|15|21|31|33|34))*\.jpg#',
            $content,
            'The header image filename should follow the pattern: {biome}_{optional_building_ids}.jpg'
        );
    }

    /**
     * Test that the header filename includes building IDs when buildings are present.
     *
     * @return void
     */
    public function testFacilitiesPageHeaderIncludesBuildingIds(): void
    {
        // Build some facilities that should appear in the header
        $this->planetSetObjectLevel('robot_factory', 1);  // ID 14
        $this->planetSetObjectLevel('shipyard', 1);        // ID 15
        $this->planetSetObjectLevel('research_lab', 1);    // ID 21

        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();
        if ($content === false) {
            $content = '';
        }

        // The header should include building IDs 14, 15, 21 (sorted: 14_15_21)
        $this->assertMatchesRegularExpression(
            '#/img/headers/facilities/(dry|desert|normal|ice|water|gas)_14_15_21\.jpg#',
            $content,
            'The header image filename should include building IDs 14, 15, and 21'
        );
    }

    /**
     * Test that the header filename does NOT include extra closing parenthesis.
     * This specifically tests for the bug where the CSS url() function had
     * incorrect syntax like: url(/path).jpg); instead of url('/path.jpg');
     *
     * @return void
     */
    public function testFacilitiesPageHeaderCssUrlSyntaxIsValid(): void
    {
        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();
        if ($content === false) {
            $content = '';
        }

        // The broken syntax would produce: url(/img/headers/facilities/...).jpg);
        // We should NOT find this pattern
        $this->assertDoesNotMatchRegularExpression(
            '#url\([^)]+\)\.jpg\);#',
            $content,
            'The header background-image should not have the broken CSS url() syntax'
        );

        // The correct syntax should produce: url('/img/headers/facilities/....jpg');
        // or: url(/img/headers/facilities/....jpg);
        $this->assertMatchesRegularExpression(
            "#url\(['\"]?/img/headers/facilities/[^'\"]+\.jpg['\"]?\)#",
            $content,
            'The header background-image should have valid CSS url() syntax'
        );
    }
}

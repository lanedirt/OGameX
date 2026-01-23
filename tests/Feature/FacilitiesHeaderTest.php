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
            "#url\(['\"]?[^)']+\.jpg['\"]?\)#",
            $content,
            'The header background-image should have valid CSS url() syntax'
        );
    }

    /**
     * Test that the header image path follows the expected pattern.
     * The header filename should match the pattern: {biome}_{optional_building_ids}.jpg
     *
     * @return void
     */
    public function testFacilitiesPageHeaderImagePath(): void
    {
        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // Get the response content
        $content = $response->getContent();
        if ($content === false) {
            $content = '';
        }

        // Verify the header image path exists and points to facilities headers directory
        $this->assertMatchesRegularExpression(
            '#/img/headers/facilities/[^\'")]+\.jpg#',
            $content,
            'The header image should point to the facilities headers directory'
        );
    }
}

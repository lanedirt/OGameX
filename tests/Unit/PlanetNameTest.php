<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\UnitTestCase;

class PlanetNameTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPlanetService();
    }

    /**
     * Test valid planet names.
     */
    public function testValidPlanetNames(): void
    {
        $validNames = ['Mars', 'Mars-1', 'Earth 2', 'Neptune-Pluto Mars'];
        foreach ($validNames as $name) {
            $result = $this->planetService->isValidPlanetName($name);
            $this->assertTrue($result, 'The name ' . $name . ' should be valid.');
        }
    }

    /**
     * Test invalid planet names.
     */
    public function testInvalidPlanetNames(): void
    {
        $invalidNames = ['-Mars', 'Mars-', 'Mars_ 1', 'V3nus__Mars', 'Jupiter Mars  '];
        foreach ($invalidNames as $name) {
            $result = $this->planetService->isValidPlanetName($name);
            $this->assertFalse($result, 'The name ' . $name . ' should be invalid.');
        }
    }
}

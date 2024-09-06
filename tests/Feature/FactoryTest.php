<?php

namespace Tests\Feature;

use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use Tests\AccountTestCase;

/**
 * Test factory classes.
 *
 * Note: even though this test does not rely on a specific user session it still requires the
 * AccountTestCase base class because it tests default dependency injection behavior of the
 * Laravel IoC container which only comes into effect when a user is logged in and a default
 * PlayerService is available.
 */
class FactoryTest extends AccountTestCase
{
    /**
     * Verify that loading a planet for another user works and returns the correct player object.
     */
    public function testPlayerFactoryLoad(): void
    {
        // Return two random user ids from database.
        $playerIds = \DB::table('users')->inRandomOrder()->limit(2)->pluck('id');
        if (count($playerIds) < 2) {
            // Create users if there are not enough in the database.
            $this->createAndLoginUser();
            $this->createAndLoginUser();
            $playerIds = \DB::table('users')->inRandomOrder()->limit(2)->pluck('id');
        }

        // Get the player service factory.
        $playerServiceFactory =  resolve(\OGame\Factories\PlayerServiceFactory::class);

        // Load the first user.
        $playerService1 = $playerServiceFactory->make($playerIds[0]);
        $this->assertEquals($playerIds[0], $playerService1->getId());

        // Load the second user.
        $playerService2 = $playerServiceFactory->make($playerIds[1]);
        $this->assertEquals($playerIds[1], $playerService2->getId());
    }

    /**
     * Verify that loading a planet for another user works and returns the correct player object.
     */
    public function testPlanetFactoryLoad(): void
    {
        // Return two random user ids from database.
        $playerIds = \DB::table('users')->inRandomOrder()->limit(2)->pluck('id');
        if (count($playerIds) < 2) {
            // Create users if there are not enough in the database.
            $this->createAndLoginUser();
            $this->createAndLoginUser();
            $playerIds = \DB::table('users')->inRandomOrder()->limit(2)->pluck('id');
        }

        // Get the first planet of each user.
        $planet1 = Planet::where('user_id', $playerIds[0])->first();
        $planet2 = Planet::where('user_id', $playerIds[1])->first();

        // Get the planet service factory.
        $planetServiceFactory =  resolve(PlanetServiceFactory::class);

        // Load the first planet.
        $planetService1 = $planetServiceFactory->make($planet1->id);
        $this->assertEquals($planet1->id, $planetService1->getPlanetId());
        $this->assertEquals($playerIds[0], $planetService1->getPlayer()->getId());

        // Load the second planet.
        $planetService2 = $planetServiceFactory->make($planet2->id);
        $this->assertEquals($planet2->id, $planetService2->getPlanetId());
        $this->assertEquals($playerIds[1], $planetService2->getPlayer()->getId());
    }
}

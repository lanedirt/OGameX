<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Test that the Hamill Manoeuvre (Light Fighter vs Deathstar) works correctly.
 */
class HamillManoeuvreTest extends AccountTestCase
{
    protected int $userPlanetAmount = 2;

    /**
     * Set up the test - create a second planet for battle testing.
     *
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a second planet for the same user to act as defender
        $planetServiceFactory = resolve(\OGame\Factories\PlanetServiceFactory::class);
        $player = $this->planetService->getPlayer();

        // Determine a new planet position
        $coordinate = $planetServiceFactory->determineNewPlanetPosition();

        // Create the additional planet
        $this->secondPlanetService = $planetServiceFactory->createAdditionalPlanetForPlayer($player, $coordinate);
    }

    /**
     * Test that General class Light Fighter can destroy a Deathstar with Hamill Manoeuvre.
     *
     * @throws BindingResolutionException
     */
    public function testHamillManoeuvreTriggersForGeneral(): void
    {
        // Set up General class player
        $player = $this->planetService->getPlayer();
        $user = $player->getUser();
        $user->character_class = 2; // General class
        $user->save();

        // Set Hamill Manoeuvre probability to 100% for testing (1 in 1)
        $settingsService = app(SettingsService::class);
        $settingsService->set('hamill_manoeuvre_chance', 1);

        // Create defender planet with 1 Deathstar
        $defenderPlanet = $this->secondPlanetService;
        $defenderPlanet->addUnit('deathstar', 1);

        // Create attacker fleet with Light Fighters
        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 100);

        // Create battle engine
        $battleEngine = new PhpBattleEngine(
            $attackerFleet,
            $player,
            $defenderPlanet,
            $settingsService,
            0,
            $player->getId()
        );

        // Run battle simulation
        $result = $battleEngine->simulateBattle();

        // Verify Hamill Manoeuvre triggered
        $this->assertTrue($result->hamillManoeuvreTriggered, 'Hamill Manoeuvre should have triggered');

        // Verify Deathstar was present at start
        $this->assertEquals(1, $result->defenderUnitsStart->getAmountByMachineName('deathstar'), 'Deathstar should be in starting units');

        // Verify Deathstar was counted as lost (Hamill Manoeuvre destroyed it)
        $this->assertGreaterThanOrEqual(1, $result->defenderUnitsLost->getAmountByMachineName('deathstar'), 'At least 1 Deathstar should be in lost units due to Hamill Manoeuvre');
    }

    /**
     * Test that non-General class cannot trigger Hamill Manoeuvre.
     *
     * @throws BindingResolutionException
     */
    public function testHamillManoeuvreDoesNotTriggerForNonGeneral(): void
    {
        // Set up Collector class player
        $player = $this->planetService->getPlayer();
        $user = $player->getUser();
        $user->character_class = 1; // Collector class
        $user->save();

        // Set Hamill Manoeuvre probability to 100% for testing
        $settingsService = app(SettingsService::class);
        $settingsService->set('hamill_manoeuvre_chance', 1);

        // Create defender planet with 1 Deathstar
        $defenderPlanet = $this->secondPlanetService;
        $defenderPlanet->addUnit('deathstar', 1);

        // Create attacker fleet with Light Fighters
        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 100);

        // Create battle engine
        $battleEngine = new PhpBattleEngine(
            $attackerFleet,
            $player,
            $defenderPlanet,
            $settingsService,
            0,
            $player->getId()
        );

        // Run battle simulation
        $result = $battleEngine->simulateBattle();

        // Verify Hamill Manoeuvre did NOT trigger
        $this->assertFalse($result->hamillManoeuvreTriggered, 'Hamill Manoeuvre should not trigger for non-General class');

        // Verify Deathstar is still present
        $this->assertEquals(1, $result->defenderUnitsStart->getAmountByMachineName('deathstar'), 'Deathstar should still be in starting units');
    }

    /**
     * Test that Hamill Manoeuvre requires Light Fighters to be present.
     *
     * @throws BindingResolutionException
     */
    public function testHamillManoeuvreRequiresLightFighters(): void
    {
        // Set up General class player
        $player = $this->planetService->getPlayer();
        $user = $player->getUser();
        $user->character_class = 2; // General class
        $user->save();

        // Set Hamill Manoeuvre probability to 100% for testing
        $settingsService = app(SettingsService::class);
        $settingsService->set('hamill_manoeuvre_chance', 1);

        // Create defender planet with 1 Deathstar
        $defenderPlanet = $this->secondPlanetService;
        $defenderPlanet->addUnit('deathstar', 1);

        // Create attacker fleet WITHOUT Light Fighters (use Heavy Fighters instead)
        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getShipObjectByMachineName('heavy_fighter'), 100);

        // Create battle engine
        $battleEngine = new PhpBattleEngine(
            $attackerFleet,
            $player,
            $defenderPlanet,
            $settingsService,
            0,
            $player->getId()
        );

        // Run battle simulation
        $result = $battleEngine->simulateBattle();

        // Verify Hamill Manoeuvre did NOT trigger
        $this->assertFalse($result->hamillManoeuvreTriggered, 'Hamill Manoeuvre should not trigger without Light Fighters');

        // Verify Deathstar is still present
        $this->assertEquals(1, $result->defenderUnitsStart->getAmountByMachineName('deathstar'), 'Deathstar should still be in starting units');
    }

    /**
     * Test that Hamill Manoeuvre requires defender to have a Deathstar.
     *
     * @throws BindingResolutionException
     */
    public function testHamillManoeuvreRequiresDeathstar(): void
    {
        // Set up General class player
        $player = $this->planetService->getPlayer();
        $user = $player->getUser();
        $user->character_class = 2; // General class
        $user->save();

        // Set Hamill Manoeuvre probability to 100% for testing
        $settingsService = app(SettingsService::class);
        $settingsService->set('hamill_manoeuvre_chance', 1);

        // Create defender planet WITHOUT Deathstar
        $defenderPlanet = $this->secondPlanetService;
        $defenderPlanet->addUnit('battle_ship', 10);

        // Create attacker fleet with Light Fighters
        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 100);

        // Create battle engine
        $battleEngine = new PhpBattleEngine(
            $attackerFleet,
            $player,
            $defenderPlanet,
            $settingsService,
            0,
            $player->getId()
        );

        // Run battle simulation
        $result = $battleEngine->simulateBattle();

        // Verify Hamill Manoeuvre did NOT trigger
        $this->assertFalse($result->hamillManoeuvreTriggered, 'Hamill Manoeuvre should not trigger without Deathstar');
    }

    /**
     * Test that Hamill Manoeuvre only destroys ONE Deathstar.
     *
     * @throws BindingResolutionException
     */
    public function testHamillManoeuvreDestroysOnlyOneDeathstar(): void
    {
        // Set up General class player
        $player = $this->planetService->getPlayer();
        $user = $player->getUser();
        $user->character_class = 2; // General class
        $user->save();

        // Set Hamill Manoeuvre probability to 100% for testing
        $settingsService = app(SettingsService::class);
        $settingsService->set('hamill_manoeuvre_chance', 1);

        // Create defender planet with 3 Deathstars
        $defenderPlanet = $this->secondPlanetService;
        $defenderPlanet->addUnit('deathstar', 3);

        // Create attacker fleet with Light Fighters
        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 100);

        // Create battle engine
        $battleEngine = new PhpBattleEngine(
            $attackerFleet,
            $player,
            $defenderPlanet,
            $settingsService,
            0,
            $player->getId()
        );

        // Run battle simulation
        $result = $battleEngine->simulateBattle();

        // Verify Hamill Manoeuvre triggered
        $this->assertTrue($result->hamillManoeuvreTriggered, 'Hamill Manoeuvre should have triggered');

        // Verify all 3 Deathstars were present at start
        $this->assertEquals(3, $result->defenderUnitsStart->getAmountByMachineName('deathstar'), '3 Deathstars should be in starting units');

        // Verify exactly ONE Deathstar was lost due to Hamill Manoeuvre
        // (The test ensures only 1 is destroyed by Hamill Manoeuvre, not by battle)
        $this->assertEquals(1, $result->defenderUnitsLost->getAmountByMachineName('deathstar'), 'Exactly 1 Deathstar should be lost due to Hamill Manoeuvre');
    }
}

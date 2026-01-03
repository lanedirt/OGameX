<?php

namespace Tests\Unit;

use OGame\Enums\CharacterClass;
use OGame\Models\User;
use OGame\Services\CharacterClassService;
use OGame\Services\DarkMatterService;
use OGame\Services\SettingsService;
use Tests\TestCase;

class CharacterClassServiceTest extends TestCase
{
    private CharacterClassService $characterClassService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the dependencies
        $darkMatterService = $this->createMock(DarkMatterService::class);
        $settingsService = $this->createMock(SettingsService::class);

        $this->characterClassService = new CharacterClassService($darkMatterService, $settingsService);
    }

    // ==================== Class Detection Tests ====================

    public function testIsCollector(): void
    {
        $user = new User();
        $user->character_class = CharacterClass::COLLECTOR->value;

        $this->assertTrue($this->characterClassService->isCollector($user));
        $this->assertFalse($this->characterClassService->isGeneral($user));
        $this->assertFalse($this->characterClassService->isDiscoverer($user));
    }

    public function testIsGeneral(): void
    {
        $user = new User();
        $user->character_class = CharacterClass::GENERAL->value;

        $this->assertFalse($this->characterClassService->isCollector($user));
        $this->assertTrue($this->characterClassService->isGeneral($user));
        $this->assertFalse($this->characterClassService->isDiscoverer($user));
    }

    public function testIsDiscoverer(): void
    {
        $user = new User();
        $user->character_class = CharacterClass::DISCOVERER->value;

        $this->assertFalse($this->characterClassService->isCollector($user));
        $this->assertFalse($this->characterClassService->isGeneral($user));
        $this->assertTrue($this->characterClassService->isDiscoverer($user));
    }

    // ==================== Collector Bonuses ====================

    public function testCollectorMineProductionBonus(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(1.25, $this->characterClassService->getMineProductionBonus($collector));
        $this->assertEquals(1.0, $this->characterClassService->getMineProductionBonus($general));
    }

    public function testCollectorEnergyProductionBonus(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(1.10, $this->characterClassService->getEnergyProductionBonus($collector));
        $this->assertEquals(1.0, $this->characterClassService->getEnergyProductionBonus($general));
    }

    public function testCollectorTransporterSpeedBonus(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(2.0, $this->characterClassService->getTransporterSpeedBonus($collector));
        $this->assertEquals(1.0, $this->characterClassService->getTransporterSpeedBonus($general));
    }

    public function testCollectorTransporterCargoBonus(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(1.25, $this->characterClassService->getTransporterCargoBonus($collector));
        $this->assertEquals(1.0, $this->characterClassService->getTransporterCargoBonus($general));
    }

    public function testCollectorCrawlerBonusMultiplier(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(1.5, $this->characterClassService->getCrawlerBonusMultiplier($collector));
        $this->assertEquals(1.0, $this->characterClassService->getCrawlerBonusMultiplier($general));
    }

    public function testCollectorMaxCrawlerOverload(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(150, $this->characterClassService->getMaxCrawlerOverload($collector));
        $this->assertEquals(100, $this->characterClassService->getMaxCrawlerOverload($general));
    }

    // ==================== General Bonuses ====================

    public function testGeneralCombatShipSpeedBonus(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2.0, $this->characterClassService->getCombatShipSpeedBonus($general));
        $this->assertEquals(1.0, $this->characterClassService->getCombatShipSpeedBonus($collector));
    }

    public function testGeneralRecyclerSpeedBonus(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2.0, $this->characterClassService->getRecyclerSpeedBonus($general));
        $this->assertEquals(1.0, $this->characterClassService->getRecyclerSpeedBonus($collector));
    }

    public function testGeneralDeuteriumConsumptionMultiplier(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.5, $this->characterClassService->getDeuteriumConsumptionMultiplier($general));
        $this->assertEquals(1.0, $this->characterClassService->getDeuteriumConsumptionMultiplier($collector));
    }

    public function testGeneralRecyclerPathfinderCargoBonus(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(1.20, $this->characterClassService->getRecyclerPathfinderCargoBonus($general));
        $this->assertEquals(1.0, $this->characterClassService->getRecyclerPathfinderCargoBonus($collector));
    }

    public function testGeneralAdditionalCombatResearchLevels(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2, $this->characterClassService->getAdditionalCombatResearchLevels($general));
        $this->assertEquals(0, $this->characterClassService->getAdditionalCombatResearchLevels($collector));
    }

    public function testGeneralAdditionalFleetSlots(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2, $this->characterClassService->getAdditionalFleetSlots($general));
        $this->assertEquals(0, $this->characterClassService->getAdditionalFleetSlots($collector));
    }

    public function testGeneralAdditionalMoonFields(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(5, $this->characterClassService->getAdditionalMoonFields($general));
        $this->assertEquals(0, $this->characterClassService->getAdditionalMoonFields($collector));
    }

    public function testGeneralHasDetailedFleetSpeedSettings(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertTrue($this->characterClassService->hasDetailedFleetSpeedSettings($general));
        $this->assertFalse($this->characterClassService->hasDetailedFleetSpeedSettings($collector));
    }

    // ==================== Discoverer Bonuses ====================

    public function testDiscovererResearchTimeMultiplier(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.75, $this->characterClassService->getResearchTimeMultiplier($discoverer));
        $this->assertEquals(1.0, $this->characterClassService->getResearchTimeMultiplier($collector));
    }

    public function testDiscovererExpeditionResourceMultiplier(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $economySpeed = 5.0;

        $this->assertEquals(7.5, $this->characterClassService->getExpeditionResourceMultiplier($discoverer, $economySpeed));
        $this->assertEquals(1.0, $this->characterClassService->getExpeditionResourceMultiplier($collector, $economySpeed));
    }

    public function testDiscovererPlanetSizeBonus(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(1.10, $this->characterClassService->getPlanetSizeBonus($discoverer));
        $this->assertEquals(1.0, $this->characterClassService->getPlanetSizeBonus($collector));
    }

    public function testDiscovererAdditionalExpeditions(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2, $this->characterClassService->getAdditionalExpeditions($discoverer));
        $this->assertEquals(0, $this->characterClassService->getAdditionalExpeditions($collector));
    }

    public function testDiscovererExpeditionEnemyChanceMultiplier(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.5, $this->characterClassService->getExpeditionEnemyChanceMultiplier($discoverer));
        $this->assertEquals(1.0, $this->characterClassService->getExpeditionEnemyChanceMultiplier($collector));
    }

    public function testDiscovererPhalanxRangeBonus(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(1.20, $this->characterClassService->getPhalanxRangeBonus($discoverer));
        $this->assertEquals(1.0, $this->characterClassService->getPhalanxRangeBonus($collector));
    }

    public function testDiscovererExpeditionSlotsBonus(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(2, $this->characterClassService->getExpeditionSlotsBonus($discoverer));
        $this->assertEquals(0, $this->characterClassService->getExpeditionSlotsBonus($collector));
    }

    public function testDiscovererInactiveLootPercentage(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.75, $this->characterClassService->getInactiveLootPercentage($discoverer));
        $this->assertEquals(0.5, $this->characterClassService->getInactiveLootPercentage($collector));
    }

    public function testDiscovererHasExpeditionDebrisFieldsVisible(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertTrue($this->characterClassService->hasExpeditionDebrisFieldsVisible($discoverer));
        $this->assertFalse($this->characterClassService->hasExpeditionDebrisFieldsVisible($collector));
    }

    // ==================== Speedup Discount Tests ====================

    public function testCollectorBuildingSpeedupDiscount(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $this->assertEquals(0.9, $this->characterClassService->getSpeedupDiscount($collector, 'building'));
        $this->assertEquals(1.0, $this->characterClassService->getSpeedupDiscount($general, 'building'));
    }

    public function testGeneralShipyardSpeedupDiscount(): void
    {
        $general = new User();
        $general->character_class = CharacterClass::GENERAL->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.9, $this->characterClassService->getSpeedupDiscount($general, 'shipyard'));
        $this->assertEquals(1.0, $this->characterClassService->getSpeedupDiscount($collector, 'shipyard'));
    }

    public function testDiscovererResearchSpeedupDiscount(): void
    {
        $discoverer = new User();
        $discoverer->character_class = CharacterClass::DISCOVERER->value;

        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(0.9, $this->characterClassService->getSpeedupDiscount($discoverer, 'research'));
        $this->assertEquals(1.0, $this->characterClassService->getSpeedupDiscount($collector, 'research'));
    }

    public function testSpeedupDiscountInvalidType(): void
    {
        $collector = new User();
        $collector->character_class = CharacterClass::COLLECTOR->value;

        $this->assertEquals(1.0, $this->characterClassService->getSpeedupDiscount($collector, 'invalid_type'));
    }

    // ==================== Race Condition Prevention Tests ====================

    public function testCannotChangeClassWithActiveFleets(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot change character class while fleet missions are active');

        // Create a user with a class
        $user = \OGame\Models\User::factory()->create([
            'character_class' => CharacterClass::COLLECTOR->value,
            'character_class_free_used' => true,
        ]);

        // Create an active (unprocessed) fleet mission
        $mission = new \OGame\Models\FleetMission();
        $mission->user_id = $user->id;
        $mission->processed = 0;
        $mission->time_departure = time();
        $mission->time_arrival = time() + 3600;
        $mission->mission_type = 3; // Transport
        $mission->save();

        // Attempt to change class should fail
        $this->characterClassService->selectClass($user, CharacterClass::GENERAL);
    }

    public function testCanChangeClassWithoutActiveFleets(): void
    {
        // Create a user with a class
        $user = \OGame\Models\User::factory()->create([
            'character_class' => CharacterClass::COLLECTOR->value,
            'character_class_free_used' => false,
        ]);

        // No active fleets - change should succeed
        $this->characterClassService->selectClass($user, CharacterClass::GENERAL);

        $this->assertEquals(CharacterClass::GENERAL->value, $user->character_class);
    }

    public function testCanChangeClassWithProcessedFleets(): void
    {
        // Create a user with a class
        $user = \OGame\Models\User::factory()->create([
            'character_class' => CharacterClass::COLLECTOR->value,
            'character_class_free_used' => false,
        ]);

        // Create a processed (completed) fleet mission
        $mission = new \OGame\Models\FleetMission();
        $mission->user_id = $user->id;
        $mission->processed = 1;
        $mission->time_departure = time() - 7200;
        $mission->time_arrival = time() - 3600;
        $mission->mission_type = 3; // Transport
        $mission->save();

        // Processed fleets don't block - change should succeed
        $this->characterClassService->selectClass($user, CharacterClass::GENERAL);

        $this->assertEquals(CharacterClass::GENERAL->value, $user->character_class);
    }

    public function testCannotDeselectClassWithActiveFleets(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot deactivate character class while fleet missions are active');

        // Create a user with a class
        $user = \OGame\Models\User::factory()->create([
            'character_class' => CharacterClass::COLLECTOR->value,
        ]);

        // Create an active (unprocessed) fleet mission
        $mission = new \OGame\Models\FleetMission();
        $mission->user_id = $user->id;
        $mission->processed = 0;
        $mission->time_departure = time();
        $mission->time_arrival = time() + 3600;
        $mission->mission_type = 3; // Transport
        $mission->save();

        // Attempt to deselect class should fail
        $this->characterClassService->deselectClass($user);
    }
}

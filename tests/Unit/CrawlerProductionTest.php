<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Enums\CharacterClass;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test that the Crawler production bonus system works correctly.
 */
class CrawlerProductionTest extends UnitTestCase
{
    /**
     * Helper method to get configured metal mine object for production calculation.
     */
    private function getConfiguredMetalMine(): \OGame\GameObjects\Models\BuildingObject
    {
        $objectService = resolve(ObjectService::class);
        $metalMine = $objectService->getObjectById(1); // Metal Mine
        assert($metalMine instanceof \OGame\GameObjects\Models\BuildingObject);
        $metalMine->production->planetService = $this->planetService;
        $metalMine->production->playerService = $this->playerService;
        $metalMine->production->characterClassService = app(\OGame\Services\CharacterClassService::class);
        $metalMine->production->universe_speed = $this->settingsService->economySpeed();
        return $metalMine;
    }

    /**
     * Test that crawlers provide production bonus.
     *
     * @throws BindingResolutionException
     */
    public function testCrawlersProvideProductionBonus(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'deuterium_synthesizer' => 10,
            'crawler' => 100,
        ]);

        // Get production for metal mine
        $metalMine = $this->getConfiguredMetalMine();
        $productionIndex = $metalMine->production->calculate(10);

        // Each crawler provides 0.02% bonus (100 crawlers = 2% bonus)
        // Crawler bonus should be > 0 for metal (only metal mine produces metal)
        $this->assertGreaterThan(0, $productionIndex->crawler->metal->get(), 'Crawlers should provide metal production bonus');
    }

    /**
     * Test that crawler bonus is limited by max usable crawlers.
     *
     * @throws BindingResolutionException
     */
    public function testCrawlerBonusLimitedByMaxUsable(): void
    {
        // Build low level mines (level 5 each = 15 total * 8 = 120 max crawlers)
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
            'crystal_mine' => 5,
            'deuterium_synthesizer' => 5,
            'crawler' => 200, // More than max usable
        ]);

        // Get production with 200 crawlers
        $metalMine = $this->getConfiguredMetalMine();
        $productionIndexWith200 = $metalMine->production->calculate(5);

        // Now test with exactly max crawlers (120)
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
            'crystal_mine' => 5,
            'deuterium_synthesizer' => 5,
            'crawler' => 120,
        ]);
        $productionIndexWith120 = $metalMine->production->calculate(5);

        // Both should have the same crawler bonus because max is 120
        $this->assertEquals(
            $productionIndexWith120->crawler->metal->get(),
            $productionIndexWith200->crawler->metal->get(),
            'Crawler bonus should be capped at max usable crawlers'
        );
    }

    /**
     * Test that Collector class gets +50% crawler bonus.
     *
     * @throws BindingResolutionException
     */
    public function testCollectorClassCrawlerBonus(): void
    {
        // Test with non-Collector class
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'deuterium_synthesizer' => 10,
            'crawler' => 100,
        ]);

        $user = $this->playerService->getUser();
        $user->character_class = CharacterClass::GENERAL->value;
        // Don't save - this is a dummy user for unit tests

        $metalMine = $this->getConfiguredMetalMine();
        $productionNonCollector = $metalMine->production->calculate(10);
        $nonCollectorBonus = $productionNonCollector->crawler->metal->get();

        // Now test with Collector class
        $user->character_class = CharacterClass::COLLECTOR->value;
        // Don't save - this is a dummy user for unit tests

        $productionCollector = $metalMine->production->calculate(10);
        $collectorBonus = $productionCollector->crawler->metal->get();

        // Collector should get 1.5x the crawler bonus
        $this->assertGreaterThan(
            $nonCollectorBonus,
            $collectorBonus,
            'Collector class should get higher crawler bonus'
        );

        // The collector multiplier is applied to the base calculation before flooring,
        // so we can't just multiply the already-floored non-collector bonus.
        // Instead, verify that the ratio is approximately 1.5x (within rounding tolerance)
        $ratio = $collectorBonus / $nonCollectorBonus;
        $this->assertGreaterThanOrEqual(
            1.4,
            $ratio,
            'Collector should get at least 1.4x crawler bonus'
        );
        $this->assertLessThanOrEqual(
            1.6,
            $ratio,
            'Collector should get at most 1.6x crawler bonus'
        );
    }

    /**
     * Test that crawlers do not provide bonus without mines.
     *
     * @throws BindingResolutionException
     */
    public function testCrawlersRequireMines(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 0,
            'crystal_mine' => 0,
            'deuterium_synthesizer' => 0,
            'crawler' => 100,
        ]);

        // Get production (mines are level 0)
        $metalMine = $this->getConfiguredMetalMine();
        $productionIndex = $metalMine->production->calculate(0);

        // Crawler bonus should be 0 because max usable crawlers is 0
        $this->assertEquals(0, $productionIndex->crawler->metal->get(), 'Crawlers should not work without mines');
    }

    /**
     * Test max usable crawlers calculation formula.
     *
     * @throws BindingResolutionException
     */
    public function testMaxUsableCrawlersFormula(): void
    {
        // Max crawlers = (10 + 8 + 6) * 8 = 192
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 8,
            'deuterium_synthesizer' => 6,
            'crawler' => 192,
        ]);

        $metalMine = $this->getConfiguredMetalMine();
        $productionWith192 = $metalMine->production->calculate(10);

        // Now test with 1 more crawler (should not increase bonus)
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 8,
            'deuterium_synthesizer' => 6,
            'crawler' => 193,
        ]);
        $productionWith193 = $metalMine->production->calculate(10);

        // Bonus should be the same
        $this->assertEquals(
            $productionWith192->crawler->metal->get(),
            $productionWith193->crawler->metal->get(),
            'Max usable crawlers should be (metal + crystal + deuterium levels) * 8'
        );
    }

    /**
     * Test that crawler bonus is included in total production.
     *
     * @throws BindingResolutionException
     */
    public function testCrawlerBonusInTotalProduction(): void
    {
        // Test without crawlers
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'deuterium_synthesizer' => 10,
            'crawler' => 0,
        ]);

        $metalMine = $this->getConfiguredMetalMine();
        $productionWithout = $metalMine->production->calculate(10);
        $totalWithout = $productionWithout->total->metal->get();

        // Test with crawlers
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'deuterium_synthesizer' => 10,
            'crawler' => 100,
        ]);
        $productionWith = $metalMine->production->calculate(10);
        $totalWith = $productionWith->total->metal->get();

        // Total production should increase
        $this->assertGreaterThan(
            $totalWithout,
            $totalWith,
            'Total production should include crawler bonus'
        );

        // Increase should equal crawler bonus
        $increase = $totalWith - $totalWithout;
        $this->assertEquals(
            $productionWith->crawler->metal->get(),
            $increase,
            'Total production increase should equal crawler bonus'
        );
    }
}

<?php

namespace Tests\Feature;

use OGame\Enums\CharacterClass;
use OGame\Services\ObjectService;
use PHPUnit\Framework\AssertionFailedError;
use Tests\AccountTestCase;

/**
 * Test that the tech tree works as expected.
 */
class TechtreeTest extends AccountTestCase
{
    /**
     * Verify that techtree techinfo popups for all objects return HTTP 200.
     */
    public function testTechtreeInfoPopups(): void
    {
        // Get all objects
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=2&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (AssertionFailedError $e) {
                $this->fail('AJAX techtree info page for "' . $object->title . '" does not return HTTP 200.');
            }
        }
    }

    /**
     * Verify that techtree technology popups for all objects return HTTP 200.
     */
    public function testTechtreeTechnologyPopupsHttp200(): void
    {
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=3&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $this->fail('AJAX techtree technology page for "' . $object->title . '" does not return HTTP 200.');
            }
        }
    }

    /**
     * Verify that the technology tab shows the global category list with prerequisites status.
     */
    public function testTechtreeTechnologyTabGlobalList(): void
    {
        $object = ObjectService::getObjectByMachineName('metal_mine');

        $response = $this->get('ajax/techtree?tab=3&object_id=' . $object->id);
        $response->assertStatus(200);

        // Category headings
        $response->assertSee('data-category="building"', false);
        $response->assertSee('data-category="research"', false);
        $response->assertSee('data-category="ship"', false);
        $response->assertSee('data-category="defense"', false);
        $response->assertSee('data-category="missile"', false);
        $response->assertSee(__('t_ingame.techtree.technology_category_construction'));
        $response->assertSee(__('t_ingame.techtree.technology_category_research'));
        $response->assertSee(__('t_ingame.techtree.technology_category_ships'));
        $response->assertSee(__('t_ingame.techtree.technology_category_defense'));
        $response->assertSee(__('t_ingame.techtree.technology_category_rockets'));

        // Sample objects from each category
        $response->assertSee('class="technology sprite_before sprite_tiny metalMine overlay"', false);
        $response->assertSee('class="technology sprite_before sprite_tiny energyTechnology overlay"', false);
        $response->assertSee('class="technology sprite_before sprite_tiny fighterLight overlay"', false);
        $response->assertSee('class="technology sprite_before sprite_tiny rocketLauncher overlay"', false);
        $response->assertSee('class="technology sprite_before sprite_tiny missileInterceptor overlay"', false);

        // Fusion plant prerequisites should be unfulfilled for a fresh account.
        $fusionPlant = ObjectService::getObjectByMachineName('fusion_plant');
        $response->assertSee('class="technology sprite_before sprite_tiny fusionPlant overlay"', false);
        $response->assertSee('class="prerequisites overlay"', false);
        $content = $response->getContent();
        if ($content === false) {
            $this->fail('AJAX techtree technology page returned no content.');
        }
        $this->assertMatchesRegularExpression(
            '/<li class="fusionPlant">.*?class="unfulfilled"/s',
            $content,
            'Fusion plant prerequisites should be unfulfilled for a fresh account.'
        );

        // Meet fusion plant requirements and assert those prerequisites become fulfilled.
        $this->planetSetObjectLevel('deuterium_synthesizer', 5);
        $this->playerSetResearchLevel('energy_technology', 3);

        $response = $this->get('ajax/techtree?tab=3&object_id=' . $object->id);
        $response->assertStatus(200);
        $content = $response->getContent();
        if ($content === false) {
            $this->fail('AJAX techtree technology page returned no content.');
        }

        $this->assertMatchesRegularExpression(
            '/<li class="fusionPlant">.*?<li class="fulfilled">.*?<\/li>.*?<li class="fulfilled">/s',
            $content,
            'Fusion plant prerequisites should both be marked fulfilled after meeting requirements.'
        );
    }

    /**
     * Verify that techtree applications popups for all objects return HTTP 200.
     */
    public function testTechtreeApplicationsPopupsHttp200(): void
    {
        // Get all objects
        $objectService = new ObjectService();

        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=4&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (AssertionFailedError $e) {
                $this->fail('AJAX techtree applications page for "' . $object->title . '" does not return HTTP 200.');
            }
        }
    }

    /**
     * Verify that techtree applications popups for all objects return HTTP 200.
     */
    public function testTechtreeApplicationsPopupsLogic(): void
    {
        // User/planet without any levels/prerequisites.
        $object = ObjectService::getObjectByMachineName('laser_technology');

        $response = $this->get('ajax/techtree?tab=4&object_id=' . $object->id);
        // Assert that no prerequisites are met for any of the applications.
        $response->assertSee('data-prerequisites-met="false"', false);
        $response->assertDontSee('data-prerequisites-met="true"', false);

        // Set character class to Collector (required for Crawler)
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('No player found for planet.');
        }
        $user = $player->getUser();
        $user->character_class = CharacterClass::COLLECTOR->value;
        $user->save();

        // User/planet with all levels/prerequisites for laser technology applications.
        $this->planetSetObjectLevel('research_lab', 12);
        $this->planetSetObjectLevel('shipyard', 12);
        $this->playerSetResearchLevel('laser_technology', 12);
        $this->playerSetResearchLevel('energy_technology', 12);
        $this->playerSetResearchLevel('ion_technology', 12);
        $this->playerSetResearchLevel('hyperspace_technology', 8);
        $this->playerSetResearchLevel('shielding_technology', 8);
        $this->playerSetResearchLevel('combustion_drive', 4); // Required for Crawler
        $this->playerSetResearchLevel('armor_technology', 4); // Required for Crawler

        $response = $this->get('ajax/techtree?tab=4&object_id=' . $object->id);
        $content = $response->getContent();
        if ($content === false) {
            $this->fail('AJAX techtree applications page for "' . $object->title . '" does not return any content.');
        }
        $metCount = substr_count($content, 'data-prerequisites-met="true"');
        $notMetCount = substr_count($content, 'data-prerequisites-met="false"');

        // Assert that 5 applications are met and 1 is not (Battlecruiser missing hyperspace_drive).
        $this->assertEquals(5, $metCount);
        $this->assertEquals(1, $notMetCount);

        // Set hyperspace drive to level 5 so the battlecruiser application is also met.
        $this->playerSetResearchLevel('hyperspace_drive', 5);
        $response = $this->get('ajax/techtree?tab=4&object_id=' . $object->id);

        // Assert that all prerequisites are now met for all applications.
        $response->assertDontSee('data-prerequisites-met="false"', false);
        $response->assertSee('data-prerequisites-met="true"', false);
    }

    /**
     * Verify that defense types do not show speed, cargo, or fuel in techinfo.
     */
    public function testTechinfoPropertiesDefenseHiddenProperties(): void
    {
        // Get defense specific objects to test.
        $defenseObjects = ObjectService::getDefenseObjects();

        foreach ($defenseObjects as $defenseObject) {
            $response = $this->get('ajax/techtree?tab=2&object_id=' . $defenseObject->id);

            try {
                $response->assertStatus(200);
                $response->assertDontSee(['Speed','Cargo Capacity','Fuel usage (Deuterium)']);
            } catch (AssertionFailedError $e) {
                $this->fail('AJAX techinfo applications page for "' . $defenseObject->title . '"');
            }
        }
    }

    /**
     * Verify that non defense types do  show speed, cargo, or fuel in techinfo.
     */
    public function testTechinfoPropertiesNoneDefenseShowsHiddenDefenseProperties(): void
    {
        // Get non defense specific objects to test.
        $civilShipObjects = ObjectService::getCivilShipObjects();

        foreach ($civilShipObjects as $nonDefenseObject) {
            $response = $this->get('ajax/techtree?tab=2&object_id=' . $nonDefenseObject->id);

            try {
                $response->assertStatus(200);
                $response->assertSee(['Speed','Cargo Capacity','Fuel usage (Deuterium)']);
            } catch (AssertionFailedError $e) {
                $this->fail('AJAX techinfo applications page for "' . $nonDefenseObject->title . '"');
            }
        }
    }
}

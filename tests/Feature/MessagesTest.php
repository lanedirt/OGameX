<?php

namespace Tests\Feature;

use OGame\Factories\GameMessageFactory;
use OGame\Models\BattleReport;
use OGame\Models\EspionageReport;
use OGame\Models\Message;
use OGame\Models\Resources;
use OGame\Services\MessageService;
use OGame\Services\DebrisFieldService;
use Tests\MoonTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class MessagesTest extends MoonTestCase
{
    /**
     * Verify that a new message has been received (as part of account registration).
     */
    public function testRegistrationMessageReceived(): void
    {
        $this->assertMessageReceivedAndContains('universe', '', [
            'Welcome to OGameX!',
            'msg_new',
            'Greetings Emperor ' . $this->currentUsername . '!',
        ]);
    }

    /**
     * Test all GameMessage classes to make sure they can be instantiated and the subject and body return a non-empty string.
     */
    public function testGameMessages(): void
    {
        $gameMessages = GameMessageFactory::getAllGameMessages();
        foreach ($gameMessages as $gameMessage) {
            // Skip:
            // - espionage_report as it requires special handling and is tested separately by testEspionageReport().
            // - battle_report as it requires special handling and is tested separately by testBattleReport().
            // - expedition game messages as they require special handling and are tested separately by testExpeditionGameMessageTranslationVariations().
            if ($gameMessage->getKey() === 'espionage_report' || $gameMessage->getKey() === 'battle_report' || $gameMessage instanceof \OGame\GameMessages\Abstracts\ExpeditionGameMessage) {
                continue;
            }

            // Get required params and fill in random values.
            $params = $gameMessage->getParams();
            $filledParams = [];
            foreach ($params as $param) {
                $filledParams[$param] = 'test';
            }

            // Check if message raw translation for subject AND body combined contains all the required params.
            foreach ($params as $param) {
                $this->assertStringContainsString(':' . $param, __('t_messages.' . $gameMessage->getKey() . '.subject') . '--' . __('t_messages.' . $gameMessage->getKey() . '.body'), 'Lang body does not contain :' . $param . ' for ' . get_class($gameMessage));
            }

            // Create empty Message object to pass to getSubject and getBody methods.
            $message = new Message();
            $message->params = $filledParams;

            // Check that the message has a valid subject and body defined.
            $this->assertNotEmpty($gameMessage->getSubject(), 'Subject is empty for ' . get_class($gameMessage));
            $this->assertNotEmpty($gameMessage->getBody(), 'Body is empty for ' . get_class($gameMessage));

            // Also assert that it does not contains "t_messages" string as this indicates the translation is missing.
            $this->assertStringNotContainsString('t_messages', $gameMessage->getSubject(), 'Subject contains t_messages for ' . get_class($gameMessage));
            $this->assertStringNotContainsString('t_messages', $gameMessage->getBody(), 'Body contains t_messages for ' . get_class($gameMessage));
        }
    }

    /**
     * Test that player ID message placeholders are replaced correctly.
     */
    public function testMessagePlaceholdersPlayerIds(): void
    {
        // Create message model with planet and moon placeholders
        $messageModel = new Message();
        $messageModel->key = 'welcome_message';
        $messageModel->params = [
            'player' => '[player]' . $this->currentUserId . '[/player]',
        ];

        // Create game message instance
        $gameMessage = GameMessageFactory::createGameMessage($messageModel);

        // Get the body of the message which should trigger placeholder replacement
        $body = $gameMessage->getBody();

        // Assert that the player name placeholder was replaced correctly
        $this->assertStringContainsString($this->currentUsername, $body, 'Player name not found in message body');

        // Assert that the original placeholder tags are not present in the final message
        $this->assertStringNotContainsString('[player]', $body, 'Player tag still present in message body');
        $this->assertStringNotContainsString('[/player]', $body, 'Player closing tag still present in message body');
    }

    /**
     * Test that planet and moon ID placeholders are replaced correctly.
     */
    public function testMessagePlaceholdersPlanetIds(): void
    {
        // Create message model with planet and moon placeholders
        $messageModel = new Message();
        $messageModel->key = 'return_of_fleet';
        $messageModel->params = [
            'from' => '[planet]' . $this->planetService->getPlanetId() . '[/planet]',
            'to' => '[planet]' . $this->moonService->getPlanetId() . '[/planet]'
        ];

        // Create game message instance
        $gameMessage = GameMessageFactory::createGameMessage($messageModel);

        // Get the body of the message which should trigger placeholder replacement
        $body = $gameMessage->getBody();

        // Assert that the planet placeholder was replaced correctly
        $this->assertStringContainsString($this->planetService->getPlanetName(), $body, 'Planet name not found in message body');
        $this->assertStringContainsString($this->planetService->getPlanetCoordinates()->asString(), $body, 'Planet coordinates not found in message body');

        // Assert that the moon placeholder was replaced correctly
        $this->assertStringContainsString($this->moonService->getPlanetName(), $body, 'Moon name not found in message body');
        $this->assertStringContainsString($this->moonService->getPlanetCoordinates()->asString(), $body, 'Moon coordinates not found in message body');

        // Assert that the planet has the correct planet icon
        $this->assertStringContainsString('planetIcon planet', $body, 'Planet icon not found in message body');

        // Assert that the moon has the correct moon icon
        $this->assertStringContainsString('planetIcon moon', $body, 'Moon icon not found in message body');

        // Assert that the original placeholder tags are not present in the final message
        $this->assertStringNotContainsString('[planet]', $body, 'Planet tag still present in message body');
        $this->assertStringNotContainsString('[/planet]', $body, 'Planet closing tag still present in message body');
    }

    /**
     * Test that debris field and coordinates placeholders are replaced correctly at current player coordinates.
     */
    public function testMessagePlaceholdersDebrisFieldCoordinates(): void
    {
        // Create debris field at current player's planet coordinates
        $debrisField = resolve(DebrisFieldService::class);
        $debrisField->loadOrCreateForCoordinates($this->planetService->getPlanetCoordinates());
        $debrisField->appendResources(new Resources(5000, 4000, 3000, 0));
        $debrisField->save();

        // Create message model with debris field placeholder
        $messageModel = new Message();
        $messageModel->key = 'debris_field_harvest';
        $messageModel->params = [
            'to' => '[debrisfield]' . $this->planetService->getPlanetCoordinates()->asString() . '[/debrisfield]',
            'coordinates' => $this->planetService->getPlanetCoordinates()->asString(),
            'ship_name' => 'Recycler',
            'ship_amount' => '5',
            'storage_capacity' => '25000',
            'metal' => '5000',
            'crystal' => '4000',
            'deuterium' => '3000',
            'harvested_metal' => '5000',
            'harvested_crystal' => '4000',
            'harvested_deuterium' => '3000'
        ];

        // Create game message instance
        $gameMessage = GameMessageFactory::createGameMessage($messageModel);

        // Get the body of the message which should trigger placeholder replacement
        $body = $gameMessage->getBody();

        // Assert that the coordinates are present in the message
        $this->assertStringContainsString($this->planetService->getPlanetCoordinates()->asString(), $body, 'Coordinates not found in message body');

        // Assert that the debris field has the correct debris field icon
        $this->assertStringContainsString('planetIcon tf', $body, 'Debris field icon not found in message body');

        // Assert that the debris field resources are present
        $this->assertStringContainsString('5,000 Metal', $body, 'Metal amount not found in message body');
        $this->assertStringContainsString('4,000 Crystal', $body, 'Crystal amount not found in message body');
        $this->assertStringContainsString('3,000 Deuterium', $body, 'Deuterium amount not found in message body');

        // Assert that the original placeholder tags are not present in the final message
        $this->assertStringNotContainsString('[debrisfield]', $body, 'Debris field tag still present in message body');
        $this->assertStringNotContainsString('[/debrisfield]', $body, 'Debris field closing tag still present in message body');
    }

    /**
     * Test EspionageReport to make sure they can be instantiated and the subject and body return a non-empty string.
     */
    public function testEspionageReport(): void
    {
        $messageService = resolve(MessageService::class);

        // Create a new espionage report record in the db and set the espionage_report_id to its ID.
        $espionageReportId = $this->createEspionageReport();
        $messageModel = $messageService->sendEspionageReportMessageToPlayer($this->planetService->getPlayer(), $espionageReportId);
        $espionageMessage = GameMessageFactory::createGameMessage($messageModel);

        // Try to open the espionage report message via full screen AJAX request.
        $response = $this->get('/ajax/messages/' . $espionageMessage->getId());

        // Check that the response is successful and it contains the espionage report message.
        $response->assertStatus(200);
        $response->assertSee('Espionage report');
        $response->assertSee('Chance of counter-espionage');
        $response->assertSee('1,000'); // 1000 metal
        $response->assertSee('500'); // 500 crystal
        $response->assertSee('debris field'); // debris field
        $response->assertSee('5,000'); // debris field 5,000 metal
    }

    /**
     * Test BattleReport to make sure they can be instantiated and the subject and body return a non-empty string.
     */
    public function testBattleReport(): void
    {
        $messageService = resolve(MessageService::class);

        // Create a new espionage report record in the db and set the battle_report_id to its ID.
        $battleReportId = $this->createBattleReport();
        $messageModel = $messageService->sendBattleReportMessageToPlayer($this->planetService->getPlayer(), $battleReportId);
        $battleReport = GameMessageFactory::createGameMessage($messageModel);

        // Try to open the espionage report message via full screen AJAX request.
        $response = $this->get('/ajax/messages/' . $battleReport->getId());

        // Check that the response is successful and it contains the espionage report message.
        $response->assertStatus(200);
        $response->assertSee('Combat Report');

        // TODO: add more assertions here to check the content of the battle report.
    }

    /**
     * Test that all ExpeditionGameMessage implementations have their translation variations properly defined.
     */
    public function testExpeditionGameMessageTranslationVariations(): void
    {
        // Get all ExpeditionGameMessage implementations from the GameMessageFactory
        $gameMessages = GameMessageFactory::getAllGameMessages();
        $expeditionMessages = [];

        foreach ($gameMessages as $key => $gameMessage) {
            if ($gameMessage instanceof \OGame\GameMessages\Abstracts\ExpeditionGameMessage) {
                $expeditionMessages[$key] = $gameMessage;
            }
        }

        // Assert that we found expedition messages
        $this->assertNotEmpty($expeditionMessages, 'No ExpeditionGameMessage implementations found');

        // Test each expedition message
        foreach ($expeditionMessages as $key => $expeditionMessage) {
            $baseKey = $expeditionMessage->getBaseKey();
            $numberOfVariations = $expeditionMessage->getNumberOfVariations();

            // Check that the base translation key exists
            $this->assertTrue(
                \Lang::has('t_messages.' . $baseKey),
                "Base translation key 't_messages.{$baseKey}' does not exist for {$key}"
            );

            // Check that the subject translation exists
            $this->assertTrue(
                \Lang::has('t_messages.' . $baseKey . '.subject'),
                "Subject translation key 't_messages.{$baseKey}.subject' does not exist for {$key}"
            );

            // Check that each variation exists
            for ($i = 1; $i <= $numberOfVariations; $i++) {
                $variationKey = 't_messages.' . $baseKey . '.body.' . $i;
                $this->assertTrue(
                    \Lang::has($variationKey),
                    "Translation variation key '{$variationKey}' does not exist for {$key} (variation {$i} of {$numberOfVariations})"
                );

                // Also check that the translation is not empty
                $translation = __($variationKey);
                $this->assertNotEmpty(
                    $translation,
                    "Translation for '{$variationKey}' is empty for {$key}"
                );
            }

            // Check that there are no extra variations beyond what's declared
            $extraVariationKey = 't_messages.' . $baseKey . '.body.' . ($numberOfVariations + 1);
            $this->assertFalse(
                \Lang::has($extraVariationKey),
                "Extra translation variation '{$extraVariationKey}' exists but is not declared in {$key} (should only have {$numberOfVariations} variations)"
            );
        }
    }

    /**
     * Create a new battle report record in the database.
     *
     * @return int The ID of the newly created battle report.
     */
    private function createBattleReport(): int
    {
        // Get a random planet to create the battle report for.
        $foreignPlanet = $this->getNearbyForeignPlanet();

        $battleReport = new BattleReport();
        $battleReport->planet_galaxy = $foreignPlanet->getPlanetCoordinates()->galaxy;
        $battleReport->planet_system = $foreignPlanet->getPlanetCoordinates()->system;
        $battleReport->planet_position = $foreignPlanet->getPlanetCoordinates()->position;
        $battleReport->planet_user_id = $foreignPlanet->getPlayer()->getId();
        $battleReport->attacker = [
            'player_id' => $this->currentUserId,
            'resource_loss' => 20000,
            'units' => [],
            'weapon_technology' => 0,
            'shielding_technology' => 0,
            'armor_technology' => 0,
        ];
        $battleReport->defender = [
            'player_id' => $foreignPlanet->getPlayer()->getId(),
            'resource_loss' => 10000,
            'units' => [],
            'weapon_technology' => 0,
            'shielding_technology' => 0,
            'armor_technology' => 0,
        ];
        $battleReport->rounds = [];
        $battleReport->loot = ['percentage' => 50, 'metal' => 1000, 'crystal' => 500, 'deuterium' => 100];
        $battleReport->debris = ['metal' => 1000, 'crystal' => 500];
        $battleReport->repaired_defenses = [];

        $battleReport->save();

        return $battleReport->id;
    }

    /**
     * Create a new espionage report record in the database.
     *
     * @return int The ID of the newly created espionage report.
     */
    private function createEspionageReport(): int
    {
        // Get a random planet to create the espionage report for.
        $foreignPlanet = $this->getNearbyForeignPlanet();

        $espionageReport = new EspionageReport();
        $espionageReport->planet_galaxy = $foreignPlanet->getPlanetCoordinates()->galaxy;
        $espionageReport->planet_system = $foreignPlanet->getPlanetCoordinates()->system;
        $espionageReport->planet_position = $foreignPlanet->getPlanetCoordinates()->position;
        $espionageReport->planet_user_id = $foreignPlanet->getPlayer()->getId();
        $espionageReport->resources = ['metal' => 1000, 'crystal' => 500, 'deuterium' => 100, 'energy' => 1000];
        $espionageReport->debris = ['metal' => 5000, 'crystal' => 2000, 'deuterium' => 0, 'energy' => 0];
        $espionageReport->buildings = ['metal_mine' => 10, 'crystal_mine' => 10, 'deuterium_synthesizer' => 10];
        $espionageReport->research = ['energy_technology' => 10, 'laser_technology' => 10, 'ion_technology' => 10];
        $espionageReport->ships = ['light_fighter' => 10, 'heavy_fighter' => 10, 'cruiser' => 10];
        $espionageReport->defense = ['rocket_launcher' => 10, 'light_laser' => 10, 'heavy_laser' => 10];
        $espionageReport->player_info = ['player_name' => 'Test Player', 'player_status' => 'inactive'];
        $espionageReport->save();

        return $espionageReport->id;
    }

    /**
     * Test BattleReport with deleted players.
     */
    public function testBattleReportWithDeletedPlayers(): void
    {
        // Get a random planet to create the battle report for.
        $foreignPlanet = $this->getNearbyForeignPlanet();

        // Create battle report with deleted players
        $battleReport = new BattleReport();
        $battleReport->planet_galaxy = $foreignPlanet->getPlanetCoordinates()->galaxy;
        $battleReport->planet_system = $foreignPlanet->getPlanetCoordinates()->system;
        $battleReport->planet_position = $foreignPlanet->getPlanetCoordinates()->position;
        $battleReport->planet_user_id = null; // Simulate deleted defender
        $battleReport->attacker = [
            'player_id' => 99999999, // Non-existent player ID
            'resource_loss' => 20000,
            'units' => [],
            'weapon_technology' => 0,
            'shielding_technology' => 0,
            'armor_technology' => 0,
        ];

        // Create message with this battle report
        $message = new Message();
        $message->user_id = $this->currentUserId;
        $message->key = 'battle_report';
        $message->battle_report_id = $battleReport->id;
        $message->save();

        // Assert the message shows "Unknown" for both players
        $response = $this->get('/ajax/messages/' . $message->id);
        $response->assertSee('Unknown');
    }
}

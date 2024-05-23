<?php

namespace Tests\Feature;

use OGame\Factories\GameMessageFactory;
use OGame\Models\EspionageReport;
use OGame\Models\Message;
use OGame\Services\PlanetService;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class MessagesTest extends AccountTestCase
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
            // Get required params and fill in random values.
            $params = $gameMessage->getParams();
            $filledParams = [];
            foreach ($params as $param) {
                $filledParams[$param] = 'test';
            }

            // Check if message raw translation contains all the required params.
            foreach ($params as $param) {
                $this->assertStringContainsString(':' . $param, __('t_messages.' . $gameMessage->getKey() . '.body'), 'Lang body does not contain :' . $param . ' for ' . get_class($gameMessage));
            }

            // Create empty Message object to pass to getSubject and getBody methods.
            $message = new Message();
            $message->params = $filledParams;

            // For espionage report, we need to set espionage_report_id to a valid value.
            if ($gameMessage->getKey() === 'espionage_report') {
                // Create a new espionage report record in the db and set the espionage_report_id to its ID.
                $espionageReportId = $this->createEspionageReport();
                $message->espionage_report_id = $espionageReportId;
            }

            // Check that the message has a valid subject and body defined.
            $this->assertNotEmpty($gameMessage->getSubject($message), 'Subject is empty for ' . get_class($gameMessage));
            $this->assertNotEmpty($gameMessage->getBody($message), 'Body is empty for ' . get_class($gameMessage));

            // Also assert that it does not contains "t_messages" string as this indicates the translation is missing.
            $this->assertStringNotContainsString('t_messages', $gameMessage->getSubject($message), 'Subject contains t_messages for ' . get_class($gameMessage));
            $this->assertStringNotContainsString('t_messages', $gameMessage->getBody($message), 'Body contains t_messages for ' . get_class($gameMessage));
        }
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
        $espionageReport->planet_user_id = $this->currentUserId;
        $espionageReport->resources = ['metal' => 1000, 'crystal' => 500, 'deuterium' => 100, 'energy' => 1000];
        $espionageReport->buildings = ['metal_mine' => 10, 'crystal_mine' => 10, 'deuterium_synthesizer' => 10];
        $espionageReport->research = ['energy_technology' => 10, 'laser_technology' => 10, 'ion_technology' => 10];
        $espionageReport->ships = ['light_fighter' => 10, 'heavy_fighter' => 10, 'cruiser' => 10];
        $espionageReport->defense = ['rocket_launcher' => 10, 'light_laser' => 10, 'heavy_laser' => 10];
        $espionageReport->player_info = ['player_name' => 'Test Player', 'player_status' => 'inactive'];
        $espionageReport->save();

        return $espionageReport->id;
    }
}

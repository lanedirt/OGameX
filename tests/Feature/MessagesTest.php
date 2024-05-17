<?php

namespace Tests\Feature;

use OGame\Factories\GameMessageFactory;
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

            // Check that the message has a valid subject and body defined.
            $this->assertNotEmpty($gameMessage->getSubject(), 'Subject is empty for ' . get_class($gameMessage));
            $this->assertNotEmpty($gameMessage->getBody($filledParams), 'Body is empty for ' . get_class($gameMessage));

            // Also assert that it does not contains "t_messages" string as this indicates the translation is missing.
            $this->assertStringNotContainsString('t_messages', $gameMessage->getSubject(), 'Subject contains t_messages for ' . get_class($gameMessage));
            $this->assertStringNotContainsString('t_messages', $gameMessage->getBody($filledParams), 'Body contains t_messages for ' . get_class($gameMessage));
        }
    }
}

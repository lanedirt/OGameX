<?php

namespace Tests\Feature;

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
}

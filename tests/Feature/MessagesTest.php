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
        // Open any page (overview)
        $response = $this->get('/overview');

        // Assert that page contains "1 unread message(s)" text.
        $response->assertSee('1 unread message(s)');

        // Try to open the message page
        $response = $this->get('/ajax/messages?tab=universe');

        // Assert that the message page contains the message with the subject "Welcome to OGameX!"
        $response->assertSee('Welcome to OGameX!');
        // And that its unread.
        $response->assertSee('msg_new');
        // And that it contains the playername.
        $response->assertSee('Greetings Emperor ' . $this->currentUsername . '!');
    }
}

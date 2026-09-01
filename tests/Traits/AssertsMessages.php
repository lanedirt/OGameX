<?php

namespace Tests\Traits;

use OGame\Factories\GameMessageFactory;
use OGame\Models\Message;
use OGame\Services\PlayerService;

/**
 * Helpers for marking messages as read and asserting in-game messages in the frontend
 * or the database.
 *
 * @method \Illuminate\Testing\TestResponse get(string $uri, array $headers = [])
 * @method void assertEquals(mixed $expected, mixed $actual, string $message = '')
 */
trait AssertsMessages
{
    /**
     * View the messages page for the current user in order to mark all default system
     * messages as read.
     */
    protected function playerSetAllMessagesRead(): void
    {
        $response = $this->get('/ajax/messages?tab=universe');
        $response->assertStatus(200);
    }

    /**
     * Asserts that a message has been received in the frontend on the specified tab/subtab
     * and that it contains the specified text.
     *
     * @param array<int,string> $mustContain
     */
    protected function assertMessageReceivedAndContains(string $tab, string $subtab, array $mustContain): void
    {
        $response = $this->get('/overview');
        $response->assertStatus(200);

        $response = $this->get('/ajax/messages?tab=' . $tab . '&subtab=' . $subtab);
        $response->assertStatus(200);

        foreach ($mustContain as $needle) {
            $response->assertSee($needle, false);
        }
    }

    /**
     * Asserts that no message has been received in the frontend.
     */
    protected function assertMessageNotReceived(): void
    {
        $response = $this->get('/overview');
        $response->assertStatus(200);
        $response->assertSee('0 unread message(s)');
    }

    /**
     * Asserts that a message has been received in the database for a specific player and that
     * it contains the specified text.
     *
     * @param PlayerService $player The player to check for messages.
     * @param array<int,string> $mustContain The text that must be contained in the message.
     * @param int $expectedCount The amount of messages that must contain the specified text.
     */
    protected function assertMessageReceivedAndContainsDatabase(PlayerService $player, array $mustContain, int $expectedCount = 1): void
    {
        $messages = Message::where('user_id', $player->getId())
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $messagesFound = 0;
        foreach ($messages as $message) {
            $messageViewModel = GameMessageFactory::createGameMessage($message);

            $stringsFound = 0;
            foreach ($mustContain as $needle) {
                if (str_contains($messageViewModel->getBody(), $needle)) {
                    $stringsFound++;
                }
            }

            if ($stringsFound === count($mustContain)) {
                $messagesFound++;
            }
        }

        $this->assertEquals($expectedCount, $messagesFound, 'Expected ' . $expectedCount . ' messages to contain the specified text:' . implode(', ', $mustContain) . '. Found ' . $messagesFound . ' messages.');
    }
}

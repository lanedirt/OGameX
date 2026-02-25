<?php

namespace Tests\Feature;

use OGame\Models\User;
use OGame\Services\AllianceService;
use OGame\Services\BuddyService;
use OGame\Services\ChatService;
use Tests\AccountTestCase;

/**
 * Test chat system functionality.
 */
class ChatTest extends AccountTestCase
{
    /**
     * Create an alliance for the current user via the AllianceService.
     */
    private function createAllianceForCurrentUser(): \OGame\Models\Alliance
    {
        $allianceService = resolve(AllianceService::class);
        $tag = 'CT' . substr(md5(uniqid((string) mt_rand(), true)), 0, 5);
        $name = 'ChatTestAlliance ' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8);

        return $allianceService->createAlliance($this->currentUserId, $tag, $name);
    }

    /**
     * Test that the chat index page loads correctly.
     */
    public function testChatPageLoads(): void
    {
        $response = $this->get('/chat');
        $response->assertStatus(200);
        $response->assertSee('Chat');
        $response->assertSee('List of your chats');
        $response->assertSee('Player list');
    }

    /**
     * Test that the chat page shows a player chat thread when playerId is provided.
     */
    public function testChatPageShowsPlayerThread(): void
    {
        $otherUser = User::factory()->create(['username' => 'ChatPartner' . time()]);

        $response = $this->get('/chat?playerId=' . $otherUser->id);
        $response->assertStatus(200);
        $response->assertSee($otherUser->username);
        $response->assertSee('No messages yet');
    }

    /**
     * Test sending a direct message via the ChatService.
     */
    public function testSendDirectMessage(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        $message = $chatService->sendDirectMessage(
            $this->currentUserId,
            $otherUser->id,
            'Hello there!'
        );

        $this->assertNotNull($message);
        $this->assertEquals($this->currentUserId, $message->sender_id);
        $this->assertEquals($otherUser->id, $message->recipient_id);
        $this->assertEquals('Hello there!', $message->message);
        $this->assertNull($message->alliance_id);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'sender_id' => $this->currentUserId,
            'recipient_id' => $otherUser->id,
            'message' => 'Hello there!',
        ]);
    }

    /**
     * Test sending a direct message via the controller endpoint.
     */
    public function testSendDirectMessageViaController(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => $otherUser->id,
            'text' => 'Hello from controller!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $data = $response->json();
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('date', $data);
        $this->assertEquals($otherUser->id, $data['targetId']);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $this->currentUserId,
            'recipient_id' => $otherUser->id,
            'message' => 'Hello from controller!',
        ]);
    }

    /**
     * Test that sending an empty message is rejected.
     */
    public function testSendEmptyMessageRejected(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => $otherUser->id,
            'text' => '   ',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'TEXT_EMPTY']);
    }

    /**
     * Test that sending a message that is too long is rejected.
     */
    public function testSendTooLongMessageRejected(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => $otherUser->id,
            'text' => str_repeat('a', 2001),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'TEXT_TOO_LONG']);
    }

    /**
     * Test that sending a message to yourself is rejected.
     */
    public function testCannotMessageSelf(): void
    {
        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => $this->currentUserId,
            'text' => 'Talking to myself',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'SAME_USER']);
    }

    /**
     * Test that an ignored player cannot send messages.
     */
    public function testIgnoredPlayerCannotMessage(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Other user ignores current user
        $buddyService->ignorePlayer($otherUser->id, $this->currentUserId);

        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => $otherUser->id,
            'text' => 'You should not see this',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'IGNORED_USER']);
    }

    /**
     * Test getting conversation history between two players.
     */
    public function testGetConversationHistory(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // Send a few messages back and forth
        $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, 'Message 1');
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Message 2');
        $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, 'Message 3');

        $conversation = $chatService->getConversation($this->currentUserId, $otherUser->id);

        $this->assertCount(3, $conversation);
        $this->assertEquals('Message 1', $conversation[0]->message);
        $this->assertEquals('Message 2', $conversation[1]->message);
        $this->assertEquals('Message 3', $conversation[2]->message);
    }

    /**
     * Test getting conversation history via the controller endpoint.
     */
    public function testGetHistoryViaController(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, 'Test message');

        $response = $this->post('/chat/history', [
            'mode' => 2,
            'playerId' => $otherUser->id,
        ]);

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('chatItems', $data);
        $this->assertArrayHasKey('chatItemsByDateAsc', $data);
        $this->assertCount(1, $data['chatItemsByDateAsc']);
    }

    /**
     * Test marking messages as read.
     */
    public function testMarkMessagesAsRead(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // Other user sends messages to current user
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Unread 1');
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Unread 2');

        // Verify unread count
        $this->assertEquals(2, $chatService->getTotalUnreadMessageCount($this->currentUserId));

        // Mark as read
        $chatService->markAsRead($this->currentUserId, $otherUser->id);

        // Verify all read
        $this->assertEquals(0, $chatService->getTotalUnreadMessageCount($this->currentUserId));
    }

    /**
     * Test marking messages as read via the controller endpoint.
     */
    public function testMarkReadViaController(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Unread message');
        $this->assertEquals(1, $chatService->getTotalUnreadMessageCount($this->currentUserId));

        $response = $this->post('/chat/read', [
            'playerId' => $otherUser->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(0, $chatService->getTotalUnreadMessageCount($this->currentUserId));
    }

    /**
     * Test unread message counts per sender.
     */
    public function testUnreadCountsPerSender(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // User1 sends 2 messages, User2 sends 3 messages
        $chatService->sendDirectMessage($user1->id, $this->currentUserId, 'From user1 - 1');
        $chatService->sendDirectMessage($user1->id, $this->currentUserId, 'From user1 - 2');
        $chatService->sendDirectMessage($user2->id, $this->currentUserId, 'From user2 - 1');
        $chatService->sendDirectMessage($user2->id, $this->currentUserId, 'From user2 - 2');
        $chatService->sendDirectMessage($user2->id, $this->currentUserId, 'From user2 - 3');

        $counts = $chatService->getUnreadCounts($this->currentUserId);

        $this->assertEquals(2, $counts[$user1->id]);
        $this->assertEquals(3, $counts[$user2->id]);

        // Total unread
        $this->assertEquals(5, $chatService->getTotalUnreadMessageCount($this->currentUserId));

        // Unread conversation count
        $this->assertEquals(2, $chatService->getUnreadConversationCount($this->currentUserId));
    }

    /**
     * Test getting recent conversations list.
     */
    public function testGetRecentConversations(): void
    {
        $user1 = User::factory()->create(['username' => 'RecentPartner1_' . time()]);
        $user2 = User::factory()->create(['username' => 'RecentPartner2_' . time()]);
        $chatService = resolve(ChatService::class);

        $chatService->sendDirectMessage($this->currentUserId, $user1->id, 'Hello user1');
        $chatService->sendDirectMessage($user2->id, $this->currentUserId, 'Hello from user2');

        $conversations = $chatService->getRecentConversations($this->currentUserId);

        // Should have at least these 2 conversations
        $this->assertGreaterThanOrEqual(2, count($conversations));

        // Check that both partners are present
        $partnerIds = array_column($conversations, 'partner_id');
        $this->assertContains($user1->id, $partnerIds);
        $this->assertContains($user2->id, $partnerIds);

        // Verify conversation data structure
        $user1Conv = collect($conversations)->firstWhere('partner_id', $user1->id);
        $this->assertEquals($user1->username, $user1Conv['partner_name']);
        $this->assertEquals('Hello user1', $user1Conv['last_message']);
    }

    /**
     * Test canMessagePlayer returns false when player is ignored.
     */
    public function testCanMessagePlayerIgnored(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);
        $buddyService = resolve(BuddyService::class);

        // Before ignoring
        $this->assertTrue($chatService->canMessagePlayer($this->currentUserId, $otherUser->id));

        // Other user ignores current user
        $buddyService->ignorePlayer($otherUser->id, $this->currentUserId);

        // After ignoring
        $this->assertFalse($chatService->canMessagePlayer($this->currentUserId, $otherUser->id));
    }

    /**
     * Test sending a message with a reply reference.
     */
    public function testSendMessageWithReply(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // Send initial message
        $original = $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Original message');

        // Reply to it
        $reply = $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, 'My reply', $original->id);

        $this->assertEquals($original->id, $reply->reply_to_id);
        $this->assertNotNull($reply->replyTo);
        $this->assertEquals('Original message', $reply->replyTo->message);
    }

    /**
     * Test formatting messages for frontend response.
     */
    public function testFormatMessagesForFrontend(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, 'My message');
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Their message');

        $messages = $chatService->getConversation($this->currentUserId, $otherUser->id);
        $formatted = $chatService->formatMessagesForFrontend($messages, $this->currentUserId);

        $this->assertArrayHasKey('chatItems', $formatted);
        $this->assertArrayHasKey('chatItemsByDateAsc', $formatted);
        $this->assertCount(2, $formatted['chatItemsByDateAsc']);

        // First message is own (odd class)
        $firstKey = $formatted['chatItemsByDateAsc'][0];
        $this->assertEquals('odd', $formatted['chatItems'][$firstKey]['altClass']);

        // Second message is from other player (no odd class)
        $secondKey = $formatted['chatItemsByDateAsc'][1];
        $this->assertEquals('', $formatted['chatItems'][$secondKey]['altClass']);
    }

    /**
     * Test sending an alliance chat message via the controller.
     */
    public function testSendAllianceMessageViaController(): void
    {
        $alliance = $this->createAllianceForCurrentUser();

        // Reload application so the user's alliance_id is reflected in auth
        $this->reloadApplication();

        $response = $this->post('/chat/send', [
            'mode' => 3,
            'associationId' => $alliance->id,
            'text' => 'Alliance message!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $data = $response->json();
        $this->assertEquals($alliance->id, $data['targetAssociationId']);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $this->currentUserId,
            'alliance_id' => $alliance->id,
            'message' => 'Alliance message!',
        ]);
    }

    /**
     * Test that non-alliance members cannot send alliance messages.
     */
    public function testNonMemberCannotSendAllianceMessage(): void
    {
        // Create an alliance via another user so current user is NOT part of it
        $otherUser = User::factory()->create();
        $allianceService = resolve(AllianceService::class);
        $alliance = $allianceService->createAlliance($otherUser->id, 'OA' . substr(md5(uniqid()), 0, 5), 'OtherAlliance');

        $response = $this->post('/chat/send', [
            'mode' => 3,
            'associationId' => $alliance->id,
            'text' => 'Sneaky message',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'NOT_AUTHORIZED']);
    }

    /**
     * Test getting alliance chat history via the controller.
     */
    public function testGetAllianceHistoryViaController(): void
    {
        $alliance = $this->createAllianceForCurrentUser();

        $chatService = resolve(ChatService::class);
        $chatService->sendAllianceMessage($this->currentUserId, $alliance->id, 'Alliance test');

        // Reload application so the user's alliance_id is reflected in auth
        $this->reloadApplication();

        $response = $this->post('/chat/history', [
            'mode' => 4,
            'associationId' => $alliance->id,
        ]);

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('chatItems', $data);
        $this->assertCount(1, $data['chatItemsByDateAsc']);
        $this->assertEquals($alliance->id, $data['associationId']);
    }

    /**
     * Test the chat page shows alliance chat thread when allianceId is provided.
     */
    public function testChatPageShowsAllianceThread(): void
    {
        $alliance = $this->createAllianceForCurrentUser();

        // Reload application so the user's alliance_id is reflected in auth
        $this->reloadApplication();

        $response = $this->get('/chat?allianceId=' . $alliance->id);
        $response->assertStatus(200);
        $response->assertSee('Alliance Chat');
        $response->assertSee($alliance->alliance_tag);
    }

    /**
     * Test that visiting a player chat thread marks messages as read.
     */
    public function testVisitingChatThreadMarksAsRead(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // Other user sends messages
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Read me 1');
        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Read me 2');

        $this->assertEquals(2, $chatService->getTotalUnreadMessageCount($this->currentUserId));

        // Visit the chat thread
        $this->get('/chat?playerId=' . $otherUser->id);

        // Messages should now be read
        $this->assertEquals(0, $chatService->getTotalUnreadMessageCount($this->currentUserId));
    }

    /**
     * Test getting history with updateUnread flag marks messages as read.
     */
    public function testHistoryUpdateUnreadMarksAsRead(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        $chatService->sendDirectMessage($otherUser->id, $this->currentUserId, 'Unread via history');
        $this->assertEquals(1, $chatService->getTotalUnreadMessageCount($this->currentUserId));

        $this->post('/chat/history', [
            'mode' => 2,
            'playerId' => $otherUser->id,
            'updateUnread' => 1,
        ]);

        $this->assertEquals(0, $chatService->getTotalUnreadMessageCount($this->currentUserId));
    }

    /**
     * Test sending a message to a non-existent player.
     */
    public function testSendMessageToNonExistentPlayer(): void
    {
        $response = $this->post('/chat/send', [
            'mode' => 1,
            'playerId' => 999999,
            'text' => 'Hello nobody',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'INVALID_PARAMETERS']);
    }

    /**
     * Test invalid mode returns error.
     */
    public function testInvalidModeReturnsError(): void
    {
        $response = $this->post('/chat/send', [
            'mode' => 99,
            'text' => 'Invalid mode',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'INVALID_PARAMETERS']);
    }

    /**
     * Test loading more (older) messages via pagination endpoint.
     */
    public function testLoadMoreMessages(): void
    {
        $otherUser = User::factory()->create();
        $chatService = resolve(ChatService::class);

        // Send several messages
        $messages = [];
        for ($i = 1; $i <= 5; $i++) {
            $messages[] = $chatService->sendDirectMessage($this->currentUserId, $otherUser->id, "Message $i");
        }

        // Load messages before the last one
        $response = $this->post('/chat/more', [
            'playerId' => $otherUser->id,
            'beforeId' => $messages[4]->id,
        ]);

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('chatItems', $data);
        $this->assertCount(4, $data['chatItemsByDateAsc']);
    }
}

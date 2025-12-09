<?php

namespace Tests\Feature;

use OGame\Models\BuddyRequest;
use OGame\Models\User;
use OGame\Services\BuddyService;
use Tests\AccountTestCase;

/**
 * Test buddy system functionality.
 */
class BuddyTest extends AccountTestCase
{
    /**
     * Test that the buddies index page loads correctly.
     */
    public function testBuddiesPageLoads(): void
    {
        $response = $this->get('/buddies');
        $response->assertStatus(200);
        $response->assertSee('Buddies');
        $response->assertSee('My buddies');
        $response->assertSee('Buddy requests');
        $response->assertSee('Ignored Players');
    }

    /**
     * Test sending a buddy request.
     */
    public function testSendBuddyRequest(): void
    {
        // Create another user to send buddy request to
        $otherUser = User::factory()->create();

        $buddyService = resolve(BuddyService::class);

        // Send a buddy request
        $request = $buddyService->sendRequest(
            $this->currentUserId,
            $otherUser->id,
            'Let\'s be buddies!'
        );

        $this->assertNotNull($request);
        $this->assertEquals($this->currentUserId, $request->sender_user_id);
        $this->assertEquals($otherUser->id, $request->receiver_user_id);
        $this->assertEquals(BuddyRequest::STATUS_PENDING, $request->status);
        $this->assertEquals('Let\'s be buddies!', $request->message);
        $this->assertFalse($request->viewed);
    }

    /**
     * Test that duplicate buddy requests cannot be sent.
     */
    public function testCannotSendDuplicateBuddyRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send first buddy request
        $buddyService->sendRequest($this->currentUserId, $otherUser->id);

        // Try to send duplicate request
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A buddy request already exists between these users.');
        $buddyService->sendRequest($this->currentUserId, $otherUser->id);
    }

    /**
     * Test accepting a buddy request.
     */
    public function testAcceptBuddyRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request from other user to current user
        $request = $buddyService->sendRequest($otherUser->id, $this->currentUserId);

        // Accept the request
        $result = $buddyService->acceptRequest($request->id, $this->currentUserId);

        $this->assertTrue($result);

        // Reload the request to check status
        $request->refresh();
        $this->assertEquals(BuddyRequest::STATUS_ACCEPTED, $request->status);
        $this->assertTrue($request->viewed);
    }

    /**
     * Test that only the receiver can accept a buddy request.
     */
    public function testOnlyReceiverCanAcceptRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request from current user to other user
        $request = $buddyService->sendRequest($this->currentUserId, $otherUser->id);

        // Try to accept as sender (should fail)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You are not authorized to accept this request.');
        $buddyService->acceptRequest($request->id, $this->currentUserId);
    }

    /**
     * Test rejecting a buddy request.
     */
    public function testRejectBuddyRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request from other user to current user
        $request = $buddyService->sendRequest($otherUser->id, $this->currentUserId);
        $requestId = $request->id;

        // Reject the request
        $result = $buddyService->rejectRequest($requestId, $this->currentUserId);

        $this->assertTrue($result);

        // Check that the request was deleted
        $this->assertDatabaseMissing('buddy_requests', ['id' => $requestId]);
    }

    /**
     * Test canceling a sent buddy request.
     */
    public function testCancelBuddyRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request
        $request = $buddyService->sendRequest($this->currentUserId, $otherUser->id);
        $requestId = $request->id;

        // Cancel the request
        $result = $buddyService->cancelRequest($requestId, $this->currentUserId);

        $this->assertTrue($result);

        // Check that the request was deleted
        $this->assertDatabaseMissing('buddy_requests', ['id' => $requestId]);
    }

    /**
     * Test getting buddies list.
     */
    public function testGetBuddies(): void
    {
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send and accept buddy requests
        $request1 = $buddyService->sendRequest($this->currentUserId, $otherUser1->id);
        $buddyService->acceptRequest($request1->id, $otherUser1->id);

        $request2 = $buddyService->sendRequest($otherUser2->id, $this->currentUserId);
        $buddyService->acceptRequest($request2->id, $this->currentUserId);

        // Get buddies list
        $buddies = $buddyService->getBuddies($this->currentUserId);

        $this->assertCount(2, $buddies);
    }

    /**
     * Test deleting a buddy.
     */
    public function testDeleteBuddy(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send and accept buddy request
        $request = $buddyService->sendRequest($this->currentUserId, $otherUser->id);
        $buddyService->acceptRequest($request->id, $otherUser->id);

        // Verify they are buddies
        $buddies = $buddyService->getBuddies($this->currentUserId);
        $this->assertCount(1, $buddies);

        // Delete the buddy
        $result = $buddyService->deleteBuddy($otherUser->id, $this->currentUserId);
        $this->assertTrue($result);

        // Verify the buddy was removed
        $buddies = $buddyService->getBuddies($this->currentUserId);
        $this->assertCount(0, $buddies);
    }

    /**
     * Test getting received buddy requests.
     */
    public function testGetReceivedRequests(): void
    {
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send buddy requests to current user
        $buddyService->sendRequest($otherUser1->id, $this->currentUserId);
        $buddyService->sendRequest($otherUser2->id, $this->currentUserId);

        // Get received requests
        $requests = $buddyService->getReceivedRequests($this->currentUserId);

        $this->assertCount(2, $requests);
    }

    /**
     * Test getting sent buddy requests.
     */
    public function testGetSentRequests(): void
    {
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send buddy requests from current user
        $buddyService->sendRequest($this->currentUserId, $otherUser1->id);
        $buddyService->sendRequest($this->currentUserId, $otherUser2->id);

        // Get sent requests
        $requests = $buddyService->getSentRequests($this->currentUserId);

        $this->assertCount(2, $requests);
    }

    /**
     * Test getting unread requests count.
     */
    public function testGetUnreadRequestsCount(): void
    {
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send buddy requests to current user
        $buddyService->sendRequest($otherUser1->id, $this->currentUserId);
        $buddyService->sendRequest($otherUser2->id, $this->currentUserId);

        // Get unread count
        $count = $buddyService->getUnreadRequestsCount($this->currentUserId);

        $this->assertEquals(2, $count);

        // Mark as viewed
        $buddyService->markRequestsAsViewed($this->currentUserId);

        // Check count again
        $count = $buddyService->getUnreadRequestsCount($this->currentUserId);
        $this->assertEquals(0, $count);
    }

    /**
     * Test ignoring a player.
     */
    public function testIgnorePlayer(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Ignore the player
        $ignored = $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);

        $this->assertNotNull($ignored);
        $this->assertEquals($this->currentUserId, $ignored->user_id);
        $this->assertEquals($otherUser->id, $ignored->ignored_user_id);

        // Verify it's in the database
        $this->assertDatabaseHas('ignored_players', [
            'user_id' => $this->currentUserId,
            'ignored_user_id' => $otherUser->id,
        ]);
    }

    /**
     * Test that duplicate ignore cannot be added.
     */
    public function testCannotIgnorePlayerTwice(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Ignore the player first time
        $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);

        // Try to ignore again
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Player is already ignored.');
        $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);
    }

    /**
     * Test unignoring a player.
     */
    public function testUnignorePlayer(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Ignore the player
        $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);

        // Unignore the player
        $result = $buddyService->unignorePlayer($this->currentUserId, $otherUser->id);

        $this->assertTrue($result);

        // Verify it's removed from the database
        $this->assertDatabaseMissing('ignored_players', [
            'user_id' => $this->currentUserId,
            'ignored_user_id' => $otherUser->id,
        ]);
    }

    /**
     * Test getting ignored players list.
     */
    public function testGetIgnoredPlayers(): void
    {
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Ignore players
        $buddyService->ignorePlayer($this->currentUserId, $otherUser1->id);
        $buddyService->ignorePlayer($this->currentUserId, $otherUser2->id);

        // Get ignored players list
        $ignoredPlayers = $buddyService->getIgnoredPlayers($this->currentUserId);

        $this->assertCount(2, $ignoredPlayers);
    }

    /**
     * Test checking if a player is ignored.
     */
    public function testIsPlayerIgnored(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Check before ignoring
        $this->assertFalse($buddyService->isPlayerIgnored($this->currentUserId, $otherUser->id));

        // Ignore the player
        $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);

        // Check after ignoring
        $this->assertTrue($buddyService->isPlayerIgnored($this->currentUserId, $otherUser->id));
    }

    /**
     * Test searching for users.
     */
    public function testSearchUsers(): void
    {
        // Create users with specific names - use unique usernames that include random elements
        $searchPrefix = 'BuddyTest' . time();
        User::factory()->create(['username' => $searchPrefix . 'Player1']);
        User::factory()->create(['username' => $searchPrefix . 'Player2']);
        User::factory()->create(['username' => 'OtherPlayer' . time()]);

        $buddyService = resolve(BuddyService::class);

        // Search for the prefix
        $results = $buddyService->searchUsers($searchPrefix, $this->currentUserId);

        // Should find 2 users (excluding current user)
        $this->assertEquals(2, $results->count());
    }

    /**
     * Test buddy request POST action via controller - cancel request.
     */
    public function testBuddyControllerCancelRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request
        $request = $buddyService->sendRequest($this->currentUserId, $otherUser->id);

        // Cancel via controller
        $response = $this->post('/buddies', [
            'action' => 3,
            'id' => $request->id,
        ]);

        $response->assertRedirect('/buddies');
        $this->assertDatabaseMissing('buddy_requests', ['id' => $request->id]);
    }

    /**
     * Test buddy request POST action via controller - accept request.
     */
    public function testBuddyControllerAcceptRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request from other user to current user
        $request = $buddyService->sendRequest($otherUser->id, $this->currentUserId);

        // Accept via controller
        $response = $this->post('/buddies', [
            'action' => 5,
            'id' => $request->id,
        ]);

        $response->assertRedirect('/buddies');

        // Verify the request was accepted
        $this->assertDatabaseHas('buddy_requests', [
            'id' => $request->id,
            'status' => BuddyRequest::STATUS_ACCEPTED,
        ]);
    }

    /**
     * Test buddy request POST action via controller - reject request.
     */
    public function testBuddyControllerRejectRequest(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send a buddy request from other user to current user
        $request = $buddyService->sendRequest($otherUser->id, $this->currentUserId);

        // Reject via controller
        $response = $this->post('/buddies', [
            'action' => 4,
            'id' => $request->id,
        ]);

        $response->assertRedirect('/buddies');
        $this->assertDatabaseMissing('buddy_requests', ['id' => $request->id]);
    }

    /**
     * Test buddy request POST action via controller - delete buddy.
     */
    public function testBuddyControllerDeleteBuddy(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Send and accept buddy request
        $request = $buddyService->sendRequest($this->currentUserId, $otherUser->id);
        $buddyService->acceptRequest($request->id, $otherUser->id);

        // Delete via controller
        $response = $this->post('/buddies', [
            'action' => 10,
            'id' => $otherUser->id,
        ]);

        $response->assertRedirect('/buddies');
        $this->assertDatabaseMissing('buddy_requests', ['id' => $request->id]);
    }

    /**
     * Test getting online buddies count.
     */
    public function testGetOnlineBuddiesCount(): void
    {
        $buddyService = resolve(BuddyService::class);

        // Create users and make them buddies
        $onlineUser = User::factory()->create();
        $offlineUser = User::factory()->create();

        // Make both users buddies with current user
        $request1 = $buddyService->sendRequest($this->currentUserId, $onlineUser->id);
        $buddyService->acceptRequest($request1->id, $onlineUser->id);

        $request2 = $buddyService->sendRequest($this->currentUserId, $offlineUser->id);
        $buddyService->acceptRequest($request2->id, $offlineUser->id);

        // Set online user's time to now (online) - time field stores Unix timestamp as string
        // Note: time is not in fillable array, so we use direct assignment
        $onlineUser->time = (string)time();
        $onlineUser->save();
        $onlineUser->refresh();

        // Set offline user's time to 20 minutes ago (offline)
        $offlineUser->time = (string)(time() - 1200);
        $offlineUser->save();
        $offlineUser->refresh();

        // Get online buddies count
        $count = $buddyService->getOnlineBuddiesCount($this->currentUserId);

        // Should only count the online user
        $this->assertEquals(1, $count);
    }

    /**
     * Test getting online buddies list.
     */
    public function testGetOnlineBuddies(): void
    {
        $buddyService = resolve(BuddyService::class);

        // Create users and make them buddies
        $onlineUser = User::factory()->create();
        $offlineUser = User::factory()->create();

        // Make both users buddies with current user
        $request1 = $buddyService->sendRequest($this->currentUserId, $onlineUser->id);
        $buddyService->acceptRequest($request1->id, $onlineUser->id);

        $request2 = $buddyService->sendRequest($this->currentUserId, $offlineUser->id);
        $buddyService->acceptRequest($request2->id, $offlineUser->id);

        // Set online user's time to now (online) - time field stores Unix timestamp as string
        // Note: time is not in fillable array, so we use direct assignment
        $onlineUser->time = (string)time();
        $onlineUser->save();
        $onlineUser->refresh();

        // Set offline user's time to 20 minutes ago (offline)
        $offlineUser->time = (string)(time() - 1200);
        $offlineUser->save();
        $offlineUser->refresh();

        // Get online buddies
        $onlineBuddies = $buddyService->getOnlineBuddies($this->currentUserId);

        // Should only include the online user
        $this->assertCount(1, $onlineBuddies);
        /** @var \OGame\Models\BuddyRequest $firstBuddy */
        $firstBuddy = $onlineBuddies->first();
        $this->assertEquals($onlineUser->id, $firstBuddy->sender_user_id === $this->currentUserId
            ? $firstBuddy->receiver_user_id
            : $firstBuddy->sender_user_id);
    }

    /**
     * Test the online buddies API endpoint.
     */
    public function testOnlineBuddiesEndpoint(): void
    {
        $buddyService = resolve(BuddyService::class);

        // Create users and make them buddies - use unique usernames
        $timestamp = time();
        $onlineUser = User::factory()->create(['username' => 'OnlineTest' . $timestamp]);
        $offlineUser = User::factory()->create(['username' => 'OfflineTest' . $timestamp]);

        // Make both users buddies with current user
        $request1 = $buddyService->sendRequest($this->currentUserId, $onlineUser->id);
        $buddyService->acceptRequest($request1->id, $onlineUser->id);

        $request2 = $buddyService->sendRequest($this->currentUserId, $offlineUser->id);
        $buddyService->acceptRequest($request2->id, $offlineUser->id);

        // Set online user's time to now (online) - time field stores Unix timestamp as string
        // Note: time is not in fillable array, so we use direct assignment
        $onlineUser->time = (string)time();
        $onlineUser->save();

        // Set offline user's time to 20 minutes ago (offline)
        $offlineUser->time = (string)(time() - 1200);
        $offlineUser->save();

        // Call the endpoint
        $response = $this->get('/buddies/online');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
        ]);

        // Check that both buddies are in the response (online and offline)
        $data = $response->json();
        $this->assertCount(2, $data['buddies']);

        // Verify online status is correctly set
        /** @var array<string, mixed> $buddies */
        $buddies = $data['buddies'];
        $onlineBuddy = collect($buddies)->firstWhere('id', $onlineUser->id);
        $offlineBuddy = collect($buddies)->firstWhere('id', $offlineUser->id);

        $this->assertNotNull($onlineBuddy);
        $this->assertTrue($onlineBuddy['isOnline']);

        $this->assertNotNull($offlineBuddy);
        $this->assertFalse($offlineBuddy['isOnline']);
    }

    /**
     * Test ignoring a player via controller redirects to buddies page.
     */
    public function testIgnorePlayerControllerRedirect(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->post('/buddies/ignore', [
            'ignored_user_id' => $otherUser->id,
        ]);

        $response->assertRedirect('/buddies');
        $response->assertSessionHas('status', 'Player ignored successfully.');

        // Verify the player is ignored
        $this->assertDatabaseHas('ignored_players', [
            'user_id' => $this->currentUserId,
            'ignored_user_id' => $otherUser->id,
        ]);
    }

    /**
     * Test unignoring a player via controller redirects to buddies page.
     */
    public function testUnignorePlayerControllerRedirect(): void
    {
        $otherUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // First ignore the player
        $buddyService->ignorePlayer($this->currentUserId, $otherUser->id);

        $response = $this->post('/buddies/unignore', [
            'ignored_user_id' => $otherUser->id,
        ]);

        $response->assertRedirect('/buddies');
        $response->assertSessionHas('status', 'Player unignored successfully.');

        // Verify the player is no longer ignored
        $this->assertDatabaseMissing('ignored_players', [
            'user_id' => $this->currentUserId,
            'ignored_user_id' => $otherUser->id,
        ]);
    }

    /**
     * Test that ignored players cannot send buddy requests.
     */
    public function testIgnoredPlayerCannotSendBuddyRequest(): void
    {
        $ignoredUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Current user ignores the other user
        $buddyService->ignorePlayer($this->currentUserId, $ignoredUser->id);

        // Ignored user tries to send a buddy request to current user
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('Cannot send buddy request to this user.'));

        $buddyService->sendRequest($ignoredUser->id, $this->currentUserId, 'Hello!');
    }

    /**
     * Test that ignoring a player prevents them from sending buddy requests via controller.
     */
    public function testIgnoredPlayerCannotSendBuddyRequestViaController(): void
    {
        $ignoredUser = User::factory()->create();
        $buddyService = resolve(BuddyService::class);

        // Current user ignores the other user
        $buddyService->ignorePlayer($this->currentUserId, $ignoredUser->id);

        // Act as the ignored user
        $this->actingAs($ignoredUser);

        // Ignored user tries to send a buddy request to current user via controller
        $response = $this->post('/buddies/sendrequest', [
            'receiver_id' => $this->currentUserId,
            'message' => 'Hello!',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // Verify no buddy request was created
        $this->assertDatabaseMissing('buddy_requests', [
            'sender_user_id' => $ignoredUser->id,
            'receiver_user_id' => $this->currentUserId,
        ]);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use OGame\Models\ChatMessage;
use OGame\Models\Message;
use OGame\Models\User;
use Tests\TestCase;

class DeleteOldMessagesCommandTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        $this->travelTo(null);

        if (isset($this->user)) {
            Message::where('user_id', $this->user->id)->delete();
            ChatMessage::withTrashed()->where('sender_id', $this->user->id)->forceDelete();
            User::where('id', $this->user->id)->delete();
        }

        parent::tearDown();
    }

    public function test_deletes_inbox_messages_that_are_at_least_seven_days_old(): void
    {
        $this->travelTo(Date::parse('2026-05-05 12:00:00'));

        $oldMessage = $this->createInboxMessage(Date::now()->subDays(7)->subSecond());
        $cutoffMessage = $this->createInboxMessage(Date::now()->subDays(7));
        $recentMessage = $this->createInboxMessage(Date::now()->subDays(7)->addSecond());

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:delete-old-messages')->assertSuccessful();

        $this->assertDatabaseMissing('messages', ['id' => $oldMessage->id]);
        $this->assertDatabaseMissing('messages', ['id' => $cutoffMessage->id]);
        $this->assertDatabaseHas('messages', ['id' => $recentMessage->id]);
    }

    public function test_archives_chat_messages_that_are_at_least_seven_days_old(): void
    {
        $this->travelTo(Date::parse('2026-05-05 12:00:00'));

        $oldMessage = $this->createChatMessage(Date::now()->subDays(7)->subSecond());
        $cutoffMessage = $this->createChatMessage(Date::now()->subDays(7));
        $recentMessage = $this->createChatMessage(Date::now()->subDays(7)->addSecond());

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:delete-old-messages')->assertSuccessful();

        // Old messages remain in the database (soft-deleted) but are hidden from users.
        $this->assertSoftDeleted('chat_messages', ['id' => $oldMessage->id]);
        $this->assertSoftDeleted('chat_messages', ['id' => $cutoffMessage->id]);
        $this->assertNotSoftDeleted('chat_messages', ['id' => $recentMessage->id]);
    }

    private function createInboxMessage(Carbon $createdAt): Message
    {
        $message = new Message();
        $message->user_id = $this->user->id;
        $message->key = 'welcome_message';
        $message->subject = 'Test message';
        $message->body = 'Test body';
        $message->params = [];
        $message->viewed = 0;
        $message->created_at = $createdAt;
        $message->updated_at = $createdAt;
        $message->save();

        return $message;
    }

    private function createChatMessage(Carbon $createdAt): ChatMessage
    {
        $message = new ChatMessage();
        $message->sender_id = $this->user->id;
        $message->message = 'Test chat message';
        $message->created_at = $createdAt;
        $message->updated_at = $createdAt;
        $message->save();

        return $message;
    }
}

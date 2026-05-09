<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
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
        Carbon::setTestNow();

        if (isset($this->user)) {
            Message::where('user_id', $this->user->id)->delete();
            User::where('id', $this->user->id)->delete();
        }

        parent::tearDown();
    }

    public function test_deletes_messages_that_are_at_least_seven_days_old(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-05 12:00:00'));

        $oldMessage = $this->createMessage(Carbon::now()->subDays(7)->subSecond());
        $cutoffMessage = $this->createMessage(Carbon::now()->subDays(7));
        $recentMessage = $this->createMessage(Carbon::now()->subDays(7)->addSecond());

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:delete-old-messages')->assertSuccessful();

        $this->assertDatabaseMissing('messages', ['id' => $oldMessage->id]);
        $this->assertDatabaseMissing('messages', ['id' => $cutoffMessage->id]);
        $this->assertDatabaseHas('messages', ['id' => $recentMessage->id]);
    }

    private function createMessage(Carbon $createdAt): Message
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
}

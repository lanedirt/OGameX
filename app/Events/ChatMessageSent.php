<?php

namespace OGame\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OGame\Models\ChatMessage;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public ChatMessage $message,
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->message->recipient_id) {
            // Direct message - broadcast to recipient's private channel
            $channels[] = new PrivateChannel('chat.user.' . $this->message->recipient_id);
        }

        if ($this->message->alliance_id) {
            // Alliance message - broadcast to alliance channel
            $channels[] = new PrivateChannel('chat.alliance.' . $this->message->alliance_id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->message->id,
            'senderId' => $this->message->sender_id,
            'senderName' => $this->message->sender->username,
            'text' => e($this->message->message),
            'date' => $this->message->created_at->timestamp,
        ];

        if ($this->message->alliance_id) {
            $data['associationId'] = $this->message->alliance_id;
        }

        if ($this->message->replyTo) {
            $data['refAuthor'] = $this->message->replyTo->sender->username ?? 'Unknown';
            $data['refText'] = e($this->message->replyTo->message);
        }

        return $data;
    }
}

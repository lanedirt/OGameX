<?php

namespace OGame\ViewModels;

use Illuminate\Support\Carbon;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Message;

/**
 * MessageViewModel
 */
class MessageViewModel
{
    public int $id;
    public int $user_id;
    public int $type;
    public string $subject;
    public string $body;
    public int $viewed;
    public Carbon|null $created_at;
    public Carbon|null $updated_at;

    /**
     * Constructor
     */
    public function __construct(Message $message)
    {
        $this->id = $message->id;
        $this->user_id = $message->user_id;
        $this->type = $message->type;
        $this->subject = $message->subject;
        $this->body = $message->body;
        $this->viewed = $message->viewed;
        $this->created_at = $message->created_at;
        $this->updated_at = $message->updated_at;
    }

    public function getFrom(): string
    {
        // From is based on the type of the message and/or the user_id/alliance_id.
        switch ($this->type) {
            default:
                return 'Fleet Command';
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        $body = $this->body;

        // Find and replace the following placeholders:
        // [player]{playerId}[/player] with the player name.
        // [alliance]{allianceId}[/alliance] with the alliance name.
        // [planet]{planetId}[/planet] with the planet name and coordinates.

        // TODO: Implement the other placeholders.
        // TODO: add unittests to cover the placeholder replacements.
        // Pattern to match [player]{playerId}[/player] placeholders
        $body = preg_replace_callback('/\[player\](\d+)\[\/player\]/', function ($matches) {
            // Assuming getPlayerNameById is a method to get a player's name by ID
            $playerServiceFactory =  app()->make(PlayerServiceFactory::class);
            $playerService = $playerServiceFactory->make($matches[1]);

            if ($playerService) {
                $playerName = $playerService->getUsername();
            } else {
                $playerName = "Unknown Player";
            }

           return $playerName;
        }, $body);

        return nl2br($body);
    }

    public function isNew(): bool
    {
        return $this->viewed === 0;
    }

    public function getDate(): string {
        // Return in this format: 18.03.2024 14:50:38
        return $this->created_at->format('d.m.Y H:i:s');
    }
}
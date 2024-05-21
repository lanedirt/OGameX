<?php

namespace OGame\ViewModels;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Factories\GameMessageFactory;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;

/**
 * MessageViewModel
 */
class MessageViewModel
{
    public int $id;
    public int $user_id;
    public string $key;
    public ?string $subject;
    public ?string $body;
    /**
     * @var array<string, string> $params
     */
    public ?array $params;
    public int $viewed;
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    private ?GameMessage $gameMessage = null;
    private Message $message;

    /**
     * Constructor
     */
    public function __construct(Message $message)
    {
        $this->id = $message->id;
        $this->user_id = $message->user_id;
        $this->key = $message->key;
        $this->subject = $message->subject;
        $this->body = $message->body;
        $this->params = $message->params;
        $this->viewed = $message->viewed;
        $this->created_at = $message->created_at;
        $this->updated_at = $message->updated_at;

        $gameMessage = GameMessageFactory::createGameMessage($this->key);
        $this->gameMessage = $gameMessage;

        // TODO: if we have enough with just the message object itself, we can remove duplicate properties above
        // as it is already stored in the message object and does not make sense to duplicate it here.
        $this->message = $message;
    }

    /**
     * Get the message sender.
     *
     * @return string
     */
    public function getFrom(): string
    {
        return $this->gameMessage->getFrom();
    }

    /**
     * Get the message ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the message subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        if ($this->gameMessage !== null) {
            // TODO: do we need replacements here or is the subject always static?
            return $this->gameMessage->getSubject($this->message);
        }

        return $this->subject;
    }

    /**
     * Get the dynamic message body with placeholders replaced with actual values.
     */
    public function getBody(): string
    {
        if ($this->gameMessage !== null) {
            return $this->gameMessage->getBody($this->message);
        } else {
            // TODO: implement dynamic messages without templates (e.g. mass messages from admin to players)
            return '';
        }
    }

    /**
     * Check if the message has not been viewed yet.
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->viewed === 0;
    }

    /**
     * Get the message creation date.
     *
     * @return string
     */
    public function getDate(): string
    {
        // Return in this format: 18.03.2024 14:50:38
        return $this->created_at->format('d.m.Y H:i:s');
    }
}

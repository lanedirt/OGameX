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
            return $this->gameMessage->getSubject();
        }

        return $this->subject;
    }

    /**
     * Get the dynamic message body with placeholders replaced with actual values.
     *
     * @throws BindingResolutionException
     */
    public function getBody(): string
    {
        if ($this->gameMessage !== null) {
            // TODO: retrieve dynamic params from message record from DB and use them here.
            // Params are retrieved as keys not the values?
            $body = nl2br($this->gameMessage->getBody($this->params));
        } else {
            // TODO: implement dynamic messages without templates (e.g. mass messages from admin to players)
            $body = nl2br($this->body);
        }

        return $this->replacePlaceholders($body);
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

    /**
     * Replace placeholders in the message body with actual values.
     *
     * @param string $body
     * @return string
     * @throws BindingResolutionException
     */
    private function replacePlaceholders(string $body): string
    {
        // Find and replace the following placeholders:
        // [player]{playerId}[/player] with the player name.
        // [alliance]{allianceId}[/alliance] with the alliance name.
        // [planet]{planetId}[/planet] with the planet name and coordinates.
        // TODO: Implement the other placeholders.
        // TODO: add unittests to cover the placeholder replacements.
        // Pattern to match [player]{playerId}[/player] placeholders
        $body = preg_replace_callback('/\[player\](\d+)\[\/player\]/', function ($matches) {
            // Assuming getPlayerNameById is a method to get a player's name by ID
            if (!is_numeric($matches[1])) {
                return "Unknown Player";
            }

            $playerServiceFactory =  app()->make(PlayerServiceFactory::class);
            $playerService = $playerServiceFactory->make((int)$matches[1]);

            if ($playerService->getId() > 0) {
                $playerName = $playerService->getUsername();
            } else {
                $playerName = "Unknown Player";
            }

            return $playerName;
        }, $body);

        $body = preg_replace_callback('/\[planet\](\d+)\[\/planet\]/', function ($matches) {
            // Assuming getPlayerNameById is a method to get a player's name by ID
            if (!is_numeric($matches[1])) {
                return "Unknown Planet";
            }

            $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
            $planetService = $planetServiceFactory->make((int)$matches[1]);

            if ($planetService !== null) {
                $planetName = '<a href="' . route('galaxy.index', ['galaxy' => $planetService->getPlanetCoordinates()->galaxy, 'system' => $planetService->getPlanetCoordinates()->system, 'position' => $planetService->getPlanetCoordinates()->position]) . '" class="txt_link">
                                    <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>
                                ' . $planetService->getPlanetName() . ' [' . $planetService->getPlanetCoordinates()->asString() . ']</a>';
            } else {
                $planetName = "Unknown Planet";
            }

            return $planetName;
        }, $body);

        $body = preg_replace_callback('/\[coordinates\](\d+):(\d+):(\d+)\[\/coordinates\]/', function ($matches) {
            // Assuming getPlayerNameById is a method to get a player's name by ID
            if (!is_numeric($matches[1]) || !is_numeric($matches[2]) || !is_numeric($matches[3])) {
                return "Unknown Planet";
            }

            $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
            $planetService = $planetServiceFactory->makeForCoordinate(new Coordinate((int)$matches[1], (int)$matches[2], (int)$matches[3]));

            if ($planetService !== null) {
                $planetName = '<a href="' . route('galaxy.index', ['galaxy' => $planetService->getPlanetCoordinates()->galaxy, 'system' => $planetService->getPlanetCoordinates()->system, 'position' => $planetService->getPlanetCoordinates()->position]) . '" class="txt_link">
                                    <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>
                                [' . $planetService->getPlanetCoordinates()->asString() . ']</a>';
            } else {
                $planetName = "Unknown Planet";
            }

            return $planetName;
        }, $body);

        return $body;
    }
}

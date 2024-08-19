<?php

namespace OGame\GameMessages\Abstracts;

use OGame\Facades\AppUtil;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ObjectService;

/**
 * GameMessage class which contains unique parsing logic for a specific message type.
 */
abstract class GameMessage
{
    /**
     * @var string The key of the message. This is used to identify the message in the language files.
     */
    protected string $key;

    /**
     * @var array<string> The params that the message requires to be filled.
     */
    protected array $params;

    /**
     * @var string The tab of the message. This is used to group messages in the game messages page.
     */
    protected string $tab;

    /**
     * @var string The subtab of the message. This is used to group messages in the game messages page.
     */
    protected string $subtab;

    /**
     * @var Message The message model from the database.
     */
    protected Message $message;

    protected PlanetServiceFactory $planetServiceFactory;

    protected PlayerServiceFactory $playerServiceFactory;

    protected ObjectService $objects;

    /**
     * GameMessage constructor.
     *
     * @param Message $message
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PlayerServiceFactory $playerServiceFactory
     * @param ObjectService $objectService
     */
    public function __construct(Message $message, PlanetServiceFactory $planetServiceFactory, PlayerServiceFactory $playerServiceFactory, ObjectService $objectService)
    {
        // Clone the message to prevent any changes to the original message affecting this object.
        // This is important because otherwise mutations such as setting the viewed flag after loading this object
        // would affect this object's state as well.
        $this->message = clone $message;
        $this->planetServiceFactory = $planetServiceFactory;
        $this->playerServiceFactory = $playerServiceFactory;
        $this->objects = $objectService;
        $this->initialize();
    }

    /**
     * Initialize the message with the key, params, tab and subtab.
     *
     * @return void
     */
    abstract protected function initialize(): void;

    /**
     * Returns whether the message is unread.
     *
     * @return bool
     */
    public function isUnread(): bool
    {
        return !$this->message->viewed;
    }

    /**
     * Get the ID of the message.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->message->id;
    }

    /**
     * Get the key of the message.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Return the message creation date as a formatted string.
     *
     * @return string
     */
    public function getDateFormatted(): string
    {
        // Return in this format: 18.03.2024 14:50:38
        return $this->message->created_at->format('d.m.Y H:i:s');
    }

    /**
     * Returns the static sender of the message.
     *
     * @return string
     */
    public function getFrom(): string
    {
        return __('t_messages.' . $this->key . '.from');
    }

    /**
     * Returns the subject of the message.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return __('t_messages.' . $this->key. '.subject');
    }

    /**
     * Get the body of the message filled with provided params.
     *
     * @return string
     */
    public function getBody(): string
    {
        // Check if all the params are provided by checking all individual param names.
        if ($this->message->params === null) {
            $this->message->params = [];
        }
        $params = $this->checkParams($this->message->params);

        // Certain reserved params such as resources should be formatted with number_format.
        $params = $this->formatReservedParams($params);

        // Get the message body from the language files.
        $translatedBody = nl2br(__('t_messages.' . $this->key . '.body', $params));

        // Replace placeholders in translated body with actual values.
        return $this->replacePlaceholders($translatedBody);
    }

    /**
     * Get the body of the message for the full message view (overlay).
     *
     * @return string
     */
    public function getBodyFull(): string
    {
        // Default to the same body as the regular message.
        return $this->getBody();
    }

    /**
     * Returns the footer actions of the message.
     *
     * @return string
     */
    public function getFooterActions(): string
    {
        // TODO: implement footer actions for messages (e.g. attack planet, favorite message, etc).
        return '';
    }

    /**
     * Returns the footer details of the message.
     *
     * @return string
     */
    public function getFooterDetails(): string
    {
        // TODO: currently only implemented for espionage reports. If used for more message types
        // move logic to here from the espionage report class and abstract.
        return '';
    }

    /**
     * Check the provided params and fill in missing ones with "?undefined?".
     *
     * @param array<string, string> $params
     * @return array<string, string>
     */
    private function checkParams(array $params): array
    {
        // Check if all the params are provided by checking all individual param names.
        foreach ($this->params as $param) {
            if (!array_key_exists($param, $params)) {
                // Replace param in message with "?undefined?" to indicate that the param is missing.
                $params[$param] = '?undefined?';
            }
        }

        return $params;
    }

    /**
     * Format reserved params such as resources.
     *
     * @param array<string, string> $params
     * @return array<string, string>
     */
    private function formatReservedParams(array $params): array
    {
        // Certain reserved params such as resources should be formatted with number_format.
        foreach ($params as $key => $value) {
            if (in_array($key, ['metal', 'crystal', 'deuterium'])) {
                $params[$key] = AppUtil::formatNumber((int)$value);
            }
        }

        return $params;
    }

    /**
     * Get the params that the message requires to be filled.
     *
     * @return array<int, string>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get the tab of the message. This is used to group messages in the game messages page.
     *
     * @return string
     */
    public function getTab(): string
    {
        return $this->tab;
    }

    /**
     * Get the subtab of the message. This is used to group messages in the game messages page.
     *
     * @return string
     */
    public function getSubtab(): string
    {
        return $this->subtab;
    }

    /**
     * Replace placeholders in the message body with actual values.
     *
     * @param string $body
     * @return string
     */
    protected function replacePlaceholders(string $body): string
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

            $playerService = null;
            try {
                $playerServiceFactory =  app()->make(PlayerServiceFactory::class);
                $playerService = $playerServiceFactory->make((int)$matches[1]);
            } catch (\Exception $e) {
                // Do nothing
            }

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

            $planetService = null;
            try {
                $planetServiceFactory = app()->make(PlanetServiceFactory::class);
                $planetService = $planetServiceFactory->make((int)$matches[1]);
            } catch (\Exception $e) {
                // Do nothing
            }

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

            $planetService = null;
            try {
                $planetServiceFactory = app()->make(PlanetServiceFactory::class);
                $planetService = $planetServiceFactory->makeForCoordinate(new Coordinate((int)$matches[1], (int)$matches[2], (int)$matches[3]));
            } catch (\Exception $e) {
                // Do nothing
            }

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

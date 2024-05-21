<?php

namespace OGame\GameMessages\Abstracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Facades\AppUtil;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;

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

    protected $planetServiceFactory;
    protected $playerServiceFactory;

    /**
     * GameMessage constructor.
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PlayerServiceFactory $playerServiceFactory
     */
    public function __construct(PlanetServiceFactory $planetServiceFactory, PlayerServiceFactory $playerServiceFactory)
    {
        $this->planetServiceFactory = $planetServiceFactory;
        $this->playerServiceFactory = $playerServiceFactory;
        $this->initialize();
    }

    /**
     * Initialize the message with the key, params, tab and subtab.
     *
     * @return void
     */
    abstract protected function initialize(): void;

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
     * @param Message $message
     * @return string
     */
    public function getSubject(Message $message): string
    {
        return __('t_messages.' . $this->key. '.subject');
    }

    /**
     * Get the body of the message filled with provided params.
     *
     * @param Message $message
     * @return string
     * @throws BindingResolutionException
     */
    public function getBody(Message $message): string
    {
        // Check if all the params are provided by checking all individual param names.
        $params = $this->checkParams($message->params);

        // Certain reserved params such as resources should be formatted with number_format.
        $params = $this->formatReservedParams($params);

        // Get the message body from the language files.
        $translatedBody = nl2br(__('t_messages.' . $this->key . '.body', $params));

        // Replace placeholders in translated body with actual values.
        return $this->replacePlaceholders($translatedBody);
    }

    /**
     * Check the provided params and fill in missing ones with "?undefined?".
     *
     * @param array $params
     * @return array<int, string>
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
     * @param array $params
     * @return array
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
            }
            catch (\Exception $e) {
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
            }
            catch (\Exception $e) {
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
            }
            catch (\Exception $e) {
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

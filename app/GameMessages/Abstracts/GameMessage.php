<?php

namespace OGame\GameMessages\Abstracts;

use OGame\Facades\AppUtil;

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

    public function __construct()
    {
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
     * @return string
     */
    public function getSubject(): string
    {
        return __('t_messages.' . $this->key. '.subject');
    }

    /**
     * Get the body of the message filled with provided params.
     *
     * @param array<string,string> $params
     * @return string
     */
    public function getBody(array $params): string
    {
        // Check if all the params are provided by checking all individual param names.
        foreach ($this->params as $param) {
            if (!array_key_exists($param, $params)) {
                // Replace param in message with "?undefined?" to indicate that the param is missing.
                $params[$param] = '?undefined?';
            }
        }

        // Certain reserved params such as resources should be formatted with number_format.
        foreach ($params as $key => $value) {
            if (in_array($key, ['metal', 'crystal', 'deuterium'])) {
                $params[$key] = AppUtil::formatNumber((int)$value);
            }
        }

        return __('t_messages.' . $this->key . '.body', $params);
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
}

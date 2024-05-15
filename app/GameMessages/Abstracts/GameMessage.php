<?php

namespace OGame\GameMessages\Abstracts;

use OGame\Facades\AppUtil;

abstract class GameMessage
{
    protected string $key;
    protected array $params;
    protected string $tab;
    protected string $subtab;

    public function __construct()
    {
        $this->initialize();
    }

    abstract protected function initialize(): void;

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
                // Return empty string if not all params are provided.
                // We do not throw an exception here because we want to allow the message parsing to fail silently
                // in order to not let old messages break the page when new params are added later.
                // TODO: do we really want to return empty instead of replacing the missing param with a placeholder?
                // Now adding or removing a placeholder would be a breaking change for all existing messages.
                return '';
            }
        }

        // Certain reserved params such as resources should be formatted with number_format.
        foreach ($params as $key => $value) {
            if (in_array($key, ['metal', 'crystal', 'deuterium'])) {
                $params[$key] = AppUtil::formatNumber($value);
            }
        }

        return __('t_messages.' . $this->key . '.body', $params);
    }

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
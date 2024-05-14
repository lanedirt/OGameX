<?php

namespace OGame\GameMessages\Abstracts;

abstract class MessageType
{
    protected int $id;
    protected string $subjectKey;
    protected string $bodyKey;
    protected array $params;
    protected string $category;

    public function __construct()
    {
        $this->initialize();
    }

    abstract protected function initialize(): void;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the subject of the message.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return __('t_messages.' . $this->subjectKey);
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
                throw new \InvalidArgumentException('Missing param ' . $param);
            }
        }

        return __('t_messages.' . $this->bodyKey, $params);
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}
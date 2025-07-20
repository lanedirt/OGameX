<?php

namespace OGame\GameMessages\Abstracts;

abstract class ExpeditionGameMessage extends GameMessage
{
    /**
     * Set default params for all expedition game messages.
     * @var array<string>
     */
    protected array $params = [];

    /**
     * Set default tab for all expedition game messages.
     * @var string
     */
    protected string $tab = 'fleets';

    /**
     * Set default subtab for all expedition game messages.
     * @var string
     */
    protected string $subtab = 'expeditions';
}

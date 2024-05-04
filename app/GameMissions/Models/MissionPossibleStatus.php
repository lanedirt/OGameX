<?php

namespace OGame\GameMissions\Models;

class MissionPossibleStatus
{
    public bool $possible;
    public string $error;

    public function __construct(bool $possible, string $error = '')
    {
        $this->possible = $possible;
        $this->error = $error;
    }
}

<?php

namespace OGame\GameMissions\Models;

/**
 * Class MissionPossibleStatus.
 *
 * This class is used to represent the possible status of a mission.
 *
 * @package OGame\GameMissions\Models
 */
class MissionPossibleStatus
{
    /**
     * @var bool Whether the mission is possible.
     */
    public bool $possible;

    /**
     * @var string The error message if the mission is not possible.
     */
    public string $error;

    /**
     * @param bool $possible Whether the mission is possible.
     * @param string $error The error message if the mission is not possible.
     */
    public function __construct(bool $possible, string $error = '')
    {
        $this->possible = $possible;
        $this->error = $error;
    }
}

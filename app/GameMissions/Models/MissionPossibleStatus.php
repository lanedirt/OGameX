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
     * @param bool $possible Whether the mission is possible.
     * @param string $error The error message if the mission is not possible.
     */
    public function __construct(public bool $possible, public string $error = '')
    {
    }
}

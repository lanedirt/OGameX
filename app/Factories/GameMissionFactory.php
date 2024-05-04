<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\ColonisationMission;
use OGame\GameMissions\DeploymentMission;
use OGame\GameMissions\TransportMission;

class GameMissionFactory
{
    /**
     * @return array<GameMission>
     * @throws BindingResolutionException
     */
    public static function getAllMissions(): array
    {
        /*
        {
          "1": "Attack",
          "2": "ACS Attack",
          "3": "Transport",
          "4": "Deployment",
          "5": "ACS Defend",
          "6": "Espionage",
          "7": "Colonisation",
          "8": "Recycle Debris Field",
          "9": "Moon Destruction",
          "15": "Expedition"
        }
        */
        return [
            3 => app()->make(TransportMission::class),
            4 => app()->make(DeploymentMission::class),
            7 => app()->make(ColonisationMission::class),
        ];
    }

    /**
     * @param int $missionId
     * @param array<string,mixed> $dependencies
     *
     * @return GameMission
     * @throws BindingResolutionException
     */
    public static function getMissionById(int $missionId, array $dependencies): GameMission
    {
        switch ($missionId) {
            case 3:
                return app()->make(TransportMission::class, $dependencies);
            case 4:
                return app()->make(DeploymentMission::class, $dependencies);
            case 7:
                return app()->make(ColonisationMission::class, $dependencies);
        }
        $missions = self::getAllMissions();
        return $missions[$missionId];
    }
}

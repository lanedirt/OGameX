<?php

namespace OGame\Factories;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\AttackMission;
use OGame\GameMissions\ColonisationMission;
use OGame\GameMissions\DeploymentMission;
use OGame\GameMissions\EspionageMission;
use OGame\GameMissions\ExpeditionMission;
use OGame\GameMissions\RecycleMission;
use OGame\GameMissions\TransportMission;

class GameMissionFactory
{
    /**
     * @return array<GameMission>
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
            1 => resolve(AttackMission::class),
            3 => resolve(TransportMission::class),
            4 => resolve(DeploymentMission::class),
            6 => resolve(EspionageMission::class),
            7 => resolve(ColonisationMission::class),
            8 => resolve(RecycleMission::class),
            15 => resolve(ExpeditionMission::class),
        ];
    }

    /**
     * @param int $missionId
     * @param array<string,mixed> $dependencies
     *
     * @return GameMission
     */
    public static function getMissionById(int $missionId, array $dependencies): GameMission
    {
        return match ($missionId) {
            1 => resolve(AttackMission::class, $dependencies),
            3 => resolve(TransportMission::class, $dependencies),
            4 => resolve(DeploymentMission::class, $dependencies),
            6 => resolve(EspionageMission::class, $dependencies),
            7 => resolve(ColonisationMission::class, $dependencies),
            8 => resolve(RecycleMission::class, $dependencies),
            15 => resolve(ExpeditionMission::class, $dependencies),
            default => throw new \RuntimeException('Mission not found: ' . $missionId),
        };
    }
}

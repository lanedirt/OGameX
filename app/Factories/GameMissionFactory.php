<?php

namespace OGame\Factories;

use OGame\Extensions\ExtensionRegistry;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\AcsDefendMission;
use OGame\GameMissions\AttackMission;
use OGame\GameMissions\ColonisationMission;
use OGame\GameMissions\DeploymentMission;
use OGame\GameMissions\EspionageMission;
use OGame\GameMissions\ExpeditionMission;
use OGame\GameMissions\MissileMission;
use OGame\GameMissions\MoonDestructionMission;
use OGame\GameMissions\RecycleMission;
use OGame\GameMissions\TransportMission;
use RuntimeException;

class GameMissionFactory
{
    /**
     * Core mission classes keyed by mission ID.
     *
     * @return array<int, class-string<GameMission>>
     */
    private static function coreMissionClasses(): array
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
          "10": "Missile Attack",
          "15": "Expedition"
        }
        */
        return [
            1 => AttackMission::class,
            2 => AttackMission::class,
            3 => TransportMission::class,
            4 => DeploymentMission::class,
            5 => AcsDefendMission::class,
            6 => EspionageMission::class,
            7 => ColonisationMission::class,
            8 => RecycleMission::class,
            9 => MoonDestructionMission::class,
            10 => MissileMission::class,
            15 => ExpeditionMission::class,
        ];
    }

    /**
     * @return array<int, GameMission>
     */
    public static function getAllMissions(): array
    {
        $missions = [];

        foreach (self::coreMissionClasses() as $id => $class) {
            $missions[$id] = resolve($class);
        }

        foreach (app(ExtensionRegistry::class)->missions() as $id => $class) {
            if (isset($missions[$id])) {
                throw new RuntimeException(sprintf(
                    'Module mission ID [%d] conflicts with a core mission.',
                    $id,
                ));
            }

            $missions[$id] = resolve($class);
        }

        return $missions;
    }

    /**
     * @param  array<string,mixed>  $dependencies
     */
    public static function getMissionById(int $missionId, array $dependencies): GameMission
    {
        $moduleMissionClass = app(ExtensionRegistry::class)->missions()[$missionId] ?? null;

        if ($moduleMissionClass !== null) {
            if (isset(self::coreMissionClasses()[$missionId])) {
                throw new RuntimeException(sprintf(
                    'Module mission ID [%d] conflicts with a core mission.',
                    $missionId,
                ));
            }

            return resolve($moduleMissionClass, $dependencies);
        }

        $coreMissionClass = self::coreMissionClasses()[$missionId] ?? null;

        if ($coreMissionClass === null) {
            throw new RuntimeException('Mission not found: '.$missionId);
        }

        return resolve($coreMissionClass, $dependencies);
    }
}

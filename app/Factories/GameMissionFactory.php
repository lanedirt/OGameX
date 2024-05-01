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
    public static function getAllMissions(): array {
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
            app()->make(DeploymentMission::class),
            app()->make(TransportMission::class),
            app()->make(ColonisationMission::class),
        ];
    }
}
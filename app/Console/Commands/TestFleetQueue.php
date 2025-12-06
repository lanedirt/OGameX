<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameMissions\AttackMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\ObjectService;

class TestFleetQueue extends Command
{
    protected $signature = 'fleet:test-queue
                            {userId : User ID}
                            {galaxy : Target galaxy}
                            {system : Target system}
                            {position : Target position}
                            {--delay=5 : Arrival delay in seconds}';

    protected $description = 'Test fleet queue system by sending 3 attack missions to specified coordinates';

    public function handle(): int
    {
        // Get arguments
        $user_id = (int) $this->argument('userId');
        $galaxy = (int) $this->argument('galaxy');
        $system = (int) $this->argument('system');
        $position = (int) $this->argument('position');
        $delay = (int) $this->option('delay');

        // Get planet
        $planet_model = Planet::where('user_id', $user_id)->first();
        if (!$planet_model) {
            return 1;
        }

        // Setup services
        $planet_factory = app(PlanetServiceFactory::class);
        $source_planet = $planet_factory->make($planet_model->id);
        $player_factory = app(PlayerServiceFactory::class);
        $player = $player_factory->make($user_id);

        // Setup test data
        $target_coordinate = new Coordinate($galaxy, $system, $position);
        $player->setResearchLevel('computer_technology', 10);
        $source_planet->addUnit('light_fighter', 6000000);
        $source_planet->addResources(new Resources(1000000000, 1000000000, 1000000000, 0));

        $attack_mission = app(AttackMission::class);
        $missions = [];

        // Mission 1: 1M light fighters
        $units1 = new UnitCollection();
        $units1->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1000000);
        try {
            $mission1 = $attack_mission->start(
                $source_planet,
                $target_coordinate,
                PlanetType::Planet,
                $units1,
                new Resources(0, 0, 0, 0),
                $delay
            );
            $missions[] = $mission1->id;
        } catch (\Exception $e) {
        }

        // Mission 2: 2M light fighters
        $units2 = new UnitCollection();
        $units2->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2000000);
        try {
            $mission2 = $attack_mission->start(
                $source_planet,
                $target_coordinate,
                PlanetType::Planet,
                $units2,
                new Resources(0, 0, 0, 0),
                $delay
            );
            $missions[] = $mission2->id;
        } catch (\Exception $e) {
        }

        // Mission 3: 3M light fighters
        $units3 = new UnitCollection();
        $units3->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 3000000);
        try {
            $mission3 = $attack_mission->start(
                $source_planet,
                $target_coordinate,
                PlanetType::Planet,
                $units3,
                new Resources(0, 0, 0, 0),
                $delay
            );
            $missions[] = $mission3->id;
        } catch (\Exception $e) {
        }

        if (empty($missions)) {
            return 1;
        }

        return 0;
    }
}

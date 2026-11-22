<?php

namespace OGame\Services;

use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Models\UserTech;

class InitialUserDataService
{
    public function __construct(private PlayerServiceFactory $playerServiceFactory, private PlanetServiceFactory $planetServiceFactory, private SettingsService $settings)
    {
    }

    public function createFor(User $user): void
    {
        $tech = new UserTech();
        $tech->user_id = $user->id;
        $tech->save();

        $playerService = $this->playerServiceFactory->make($user->id);
        $planetNames = ['Homeworld', 'Colony'];
        for ($index = 0; $index < $this->settings->registrationPlanetAmount(); $index++) {
            $this->planetServiceFactory->createInitialPlanetForPlayer($playerService, $planetNames[$index === 0 ? 0 : 1]);
        }

        (new MessageService($playerService))->sendWelcomeMessage();
    }
}

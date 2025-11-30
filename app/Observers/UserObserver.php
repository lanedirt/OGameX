<?php

namespace OGame\Observers;

use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\User;
use OGame\Services\DarkMatterService;
use OGame\Services\SettingsService;

class UserObserver
{
    /**
     * UserObserver constructor.
     *
     * @param DarkMatterService $darkMatterService
     * @param SettingsService $settingsService
     */
    public function __construct(
        private DarkMatterService $darkMatterService,
        private SettingsService $settingsService
    ) {
    }

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        // Credit initial Dark Matter amount
        $initialAmount = (int)$this->settingsService->get('dark_matter_initial', 8000);

        if ($initialAmount > 0) {
            $this->darkMatterService->credit(
                $user,
                $initialAmount,
                DarkMatterTransactionType::INITIAL_BONUS->value,
                'Initial Dark Matter bonus on registration'
            );
        }
    }
}

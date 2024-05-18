<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class ServerSettingsController extends OGameController
{
    /**
     * Shows the server settings page.
     *
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @return View
     */
    public function index(PlayerService $player, SettingsService $settingsService): View
    {
        return view('ingame.admin.serversettings')->with([
            'fleet_speed' => $settingsService->fleetSpeed(),
            'economy_speed' => $settingsService->economySpeed(),
            'research_speed' => $settingsService->researchSpeed(),
            'basic_income_metal' => $settingsService->basicIncomeMetal(),
            'basic_income_crystal' => $settingsService->basicIncomeCrystal(),
            'basic_income_deuterium' => $settingsService->basicIncomeDeuterium(),
            'basic_income_energy' => $settingsService->basicIncomeEnergy(),
        ]);
    }

    /**
     * Updates the server settings.
     *
     * @param SettingsService $settingsService
     * @return RedirectResponse
     */
    public function update(SettingsService $settingsService): RedirectResponse
    {
        $settingsService->set('fleet_speed', request('fleet_speed'));
        $settingsService->set('economy_speed', request('economy_speed'));
        $settingsService->set('research_speed', request('research_speed'));

        $settingsService->set('basic_income_metal', request('basic_income_metal'));
        $settingsService->set('basic_income_crystal', request('basic_income_crystal'));
        $settingsService->set('basic_income_deuterium', request('basic_income_deuterium'));
        $settingsService->set('basic_income_energy', request('basic_income_energy'));

        return redirect()->route('admin.serversettings.index')->with('success', __('Changes saved!'));
    }
}

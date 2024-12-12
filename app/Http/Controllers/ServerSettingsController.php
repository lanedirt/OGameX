<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Services\SettingsService;

class ServerSettingsController extends OGameController
{
    /**
     * Shows the server settings info popup page
     *
     * @return View
     */
    public function overlay(SettingsService $settingsService): View
    {
        return view('ingame.serversettings.overlay')->with([
            'universe_name' => $settingsService->universeName(),
            'fleet_speed' => $settingsService->fleetSpeed(),
            'economy_speed' => $settingsService->economySpeed(),
            'research_speed' => $settingsService->researchSpeed(),
            'planet_fields_bonus' => $settingsService->planetFieldsBonus(),
            'dark_matter_bonus' => $settingsService->darkMatterBonus(),
            'alliance_combat_system_on' => $settingsService->allianceCombatSystemOn(),
            'debris_field_from_ships' => $settingsService->debrisFieldFromShips(),
            'debris_field_from_defense' => $settingsService->debrisFieldFromDefense(),
            'debris_field_deuterium_on' => $settingsService->debrisFieldDeuteriumOn(),
            'maximum_moon_chance' => $settingsService->maximumMoonChance(),
            'ignore_empty_systems_on' => $settingsService->ignoreEmptySystemsOn(),
            'ignore_inactive_systems_on' => $settingsService->ignoreInactiveSystemsOn(),
            'number_of_galaxies' => $settingsService->numberOfGalaxies(),
        ]);
    }
}

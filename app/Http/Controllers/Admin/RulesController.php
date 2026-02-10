<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class RulesController extends OGameController
{
    /**
     * Shows the admin rules editor page.
     *
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @return View
     */
    public function index(PlayerService $player, SettingsService $settingsService): View
    {
        return view('ingame.admin.rules')->with([
            'rules_content' => $settingsService->rulesContent(),
        ]);
    }

    /**
     * Saves the rules content.
     *
     * @param SettingsService $settingsService
     * @return RedirectResponse
     */
    public function update(SettingsService $settingsService): RedirectResponse
    {
        $settingsService->set('rules_content', request('rules_content', ''));

        return redirect()->route('admin.rules.index')->with('success', __('Changes saved!'));
    }
}

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
     * Shows the admin rules & legal editor page.
     *
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @return View
     */
    public function index(PlayerService $player, SettingsService $settingsService): View
    {
        return view('ingame.admin.rules')->with([
            'rules_content' => $settingsService->rulesContent(),
            'legal_content' => $settingsService->legalContent(),
            'privacy_policy_content' => $settingsService->privacyPolicyContent(),
            'terms_content' => $settingsService->termsContent(),
            'contact_content' => $settingsService->contactContent(),
        ]);
    }

    /**
     * Saves all content.
     *
     * @param SettingsService $settingsService
     * @return RedirectResponse
     */
    public function update(SettingsService $settingsService): RedirectResponse
    {
        $settingsService->set('rules_content', request('rules_content', ''));
        $settingsService->set('legal_content', request('legal_content', ''));
        $settingsService->set('privacy_policy_content', request('privacy_policy_content', ''));
        $settingsService->set('terms_content', request('terms_content', ''));
        $settingsService->set('contact_content', request('contact_content', ''));

        return redirect()->route('admin.rules.index')->with('success', __('Changes saved!'));
    }
}

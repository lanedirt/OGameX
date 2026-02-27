<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Services\BbCodeParserService;
use OGame\Services\SettingsService;

class RulesController extends Controller
{
    /**
     * Shows the server rules overlay (AJAX endpoint).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxRules(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        return view('outgame.rules.overlay', [
            'contentHtml' => $bbCodeParser->parse($settingsService->rulesContent()),
            'emptyMessage' => 'No rules have been set.',
        ]);
    }

    /**
     * Shows the legal overlay (AJAX endpoint).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxLegal(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        return view('outgame.rules.overlay', [
            'contentHtml' => $bbCodeParser->parse($settingsService->legalContent()),
            'emptyMessage' => 'No legal information has been set.',
        ]);
    }

    /**
     * Shows the privacy policy overlay (AJAX endpoint).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxPrivacyPolicy(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        return view('outgame.rules.overlay', [
            'contentHtml' => $bbCodeParser->parse($settingsService->privacyPolicyContent()),
            'emptyMessage' => 'No privacy policy has been set.',
        ]);
    }

    /**
     * Shows the terms and conditions overlay (AJAX endpoint).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxTerms(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        return view('outgame.rules.overlay', [
            'contentHtml' => $bbCodeParser->parse($settingsService->termsContent()),
            'emptyMessage' => 'No terms and conditions have been set.',
        ]);
    }

    /**
     * Shows the contact overlay (AJAX endpoint).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxContact(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        return view('outgame.rules.overlay', [
            'contentHtml' => $bbCodeParser->parse($settingsService->contactContent()),
            'emptyMessage' => 'No contact information has been set.',
        ]);
    }
}

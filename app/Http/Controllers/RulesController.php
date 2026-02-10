<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;
use OGame\Services\BbCodeParserService;
use OGame\Services\SettingsService;

class RulesController extends Controller
{
    /**
     * Shows the server rules overlay (AJAX endpoint for outgame page).
     *
     * @param SettingsService $settingsService
     * @param BbCodeParserService $bbCodeParser
     * @return View
     */
    public function ajaxRules(SettingsService $settingsService, BbCodeParserService $bbCodeParser): View
    {
        $bbcode = $settingsService->rulesContent();
        $rulesHtml = $bbCodeParser->parse($bbcode);

        return view('outgame.rules.overlay', [
            'rulesHtml' => $rulesHtml,
        ]);
    }
}

<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AllianceController extends OGameController
{
    /**
     * Shows the alliance index page
     *
     * @return View
     */
    public function index(): View
    {
        return view('ingame.alliance.index');
    }

    /**
     * Shows the alliance creation page
     *
     * @return View
     */
    public function create(): View
    {
        // TODO: create template.
        return view('ingame.alliance.create');
    }

    public function ajaxCreate(): JsonResponse
    {
        return response()->json([
            'content' => [
              'alliance/alliance_create' => view('ingame.alliance.create')->render(),
            ],
            'files' => [
              'js' => [],
              'css' => [],
            ],
            'newAjaxToken' => csrf_token(),
            'page' => [
              'stateObj' => [],
              'title' => 'OGameX',
              'url' => route('alliance.index'),
            ],
            'serverTime' => time(),
            'target' => 'alliance/alliance_create',
        ]);
    }
}
